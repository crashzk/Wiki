<?php

namespace Flute\Modules\Wiki\Services;

use DateTimeImmutable;
use Flute\Modules\Wiki\database\Entities\WikiArticle;
use Flute\Modules\Wiki\database\Entities\WikiCategory;
use RuntimeException;

class WikiService implements WikiServiceInterface
{
    public function getAllCategories(): array
    {
        return WikiCategory::query()
            ->load('articles')
            ->orderBy('sort_order', 'ASC')
            ->fetchAll();
    }

    public function getActiveCategories(): array
    {
        return WikiCategory::query()
            ->where('active', true)
            ->load('articles')
            ->orderBy('sort_order', 'ASC')
            ->fetchAll();
    }

    public function getPopularArticles(int $limit = 6): array
    {
        return WikiArticle::query()
            ->where('is_published', true)
            ->load('category')
            ->load('author')
            ->orderBy('views', 'DESC')
            ->limit($limit)
            ->fetchAll();
    }

    public function getRecentArticles(int $limit = 5): array
    {
        return WikiArticle::query()
            ->where('is_published', true)
            ->load('category')
            ->load('author')
            ->orderBy('updatedAt', 'DESC')
            ->limit($limit)
            ->fetchAll();
    }

    public function getTotalArticlesCount(): int
    {
        return WikiArticle::query()
            ->where('is_published', true)
            ->count();
    }

    public function getCategoryBySlug(string $slug): ?WikiCategory
    {
        return WikiCategory::query()
            ->where('slug', $slug)
            ->load('articles')
            ->fetchOne();
    }

    public function getArticleBySlug(string $slug): ?WikiArticle
    {
        return WikiArticle::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->load('category')
            ->load('author')
            ->fetchOne();
    }

    public function searchArticles(string $query): array
    {
        $search = '%' . $query . '%';

        return WikiArticle::query()
            ->where('is_published', true)
            ->where(static function ($q) use ($search) {
                $q->where('title', 'LIKE', $search)
                    ->orWhere('description', 'LIKE', $search)
                    ->orWhere('content', 'LIKE', $search);
            })
            ->load('category')
            ->orderBy('views', 'DESC')
            ->fetchAll();
    }

    public function getRelatedArticles(WikiArticle $article, int $limit = 4): array
    {
        $currentTags = $article->getTagsArray();

        if (empty($currentTags)) {
            return WikiArticle::query()
                ->where('id', '!=', $article->id)
                ->where('category_id', $article->category->id)
                ->where('is_published', true)
                ->load('category')
                ->limit($limit)
                ->orderBy('views', 'DESC')
                ->fetchAll();
        }

        $articles = WikiArticle::query()
            ->where('id', '!=', $article->id)
            ->where('is_published', true)
            ->load('category')
            ->fetchAll();

        $related = [];
        foreach ($articles as $a) {
            $articleTags = $a->getTagsArray();
            $commonTags = array_intersect($currentTags, $articleTags);
            if (!empty($commonTags)) {
                $related[] = $a;
            }
        }

        return array_slice($related, 0, $limit);
    }

    public function incrementViews(WikiArticle $article): void
    {
        $sessionKey = 'wiki_article_viewed_' . $article->id;
        $alreadyViewed = session()->has($sessionKey);

        if (!$alreadyViewed) {
            $article->incrementViews();
            session()->set($sessionKey, true);
        }
    }

    public function addFeedback(WikiArticle $article, bool $isHelpful): void
    {
        $sessionKey = 'wiki_article_feedback_' . $article->id;
        $previousVote = session()->get($sessionKey);

        if ($previousVote === $isHelpful) {
            return;
        }

        if ($previousVote !== null) {
            if ($previousVote === true) {
                $article->helpful = max(0, $article->helpful - 1);
            } else {
                $article->not_helpful = max(0, $article->not_helpful - 1);
            }
        }

        if ($isHelpful) {
            $article->addHelpful();
        } else {
            $article->addNotHelpful();
        }

        session()->set($sessionKey, $isHelpful);
    }

    public function createCategory(array $data): WikiCategory
    {
        $category = new WikiCategory();
        $category->name = $data['name'];
        $category->slug = $data['slug'] ?? $this->generateCategorySlug($data['name']);
        $category->description = $data['description'] ?? null;
        $category->icon = $data['icon'] ?? null;
        $category->active = $data['active'] ?? true;
        $category->sort_order = $data['sort_order'] ?? $this->getNextCategorySortOrder();
        $category->saveOrFail();

        return $category;
    }

    public function updateCategory(WikiCategory $category, array $data): WikiCategory
    {
        if (isset($data['name'])) {
            $category->name = $data['name'];
        }

        $category->slug = $this->generateCategorySlug($data['name']);
        $category->description = $data['description'] ?? null;
        $category->icon = $data['icon'] ?? null;
        $category->active = $data['active'] ?? true;
        $category->sort_order = $data['sort_order'] ?? $this->getNextCategorySortOrder();

        $category->saveOrFail();

        return $category;
    }

    public function deleteCategory(WikiCategory $category): bool
    {
        foreach ($category->articles as $article) {
            $article->delete();
        }

        return $category->delete();
    }

    public function createArticle(array $data): WikiArticle
    {
        $article = new WikiArticle();
        $article->title = $data['title'];
        $article->slug = $this->generateSlug($data['slug'] ?? $data['title']);
        $article->description = $data['description'] ?? null;
        $article->content = $data['content'] ?? null;
        $tags = $this->normalizeTags($data['tags'] ?? []);
        $article->tags = $tags === [] ? null : $tags;
        $article->is_published = filter_var($data['is_published'] ?? true, FILTER_VALIDATE_BOOLEAN);

        if (empty($data['category_id'])) {
            throw new RuntimeException('Category not found');
        }

        $category = WikiCategory::findByPK($data['category_id']);
        if (!$category) {
            throw new RuntimeException('Category not found');
        }

        $article->category = $category;
        $article->sort_order = $data['sort_order'] ?? $this->getNextArticleSortOrder($category->id);

        if (!empty($data['author_id'])) {
            $article->author_id = (int) $data['author_id'];
            $author = \Flute\Core\Database\Entities\User::findByPK($data['author_id']);
            $article->author = $author;
        }

        $article->saveOrFail();

        return $article;
    }

    public function updateArticle(WikiArticle $article, array $data): WikiArticle
    {
        if (isset($data['title'])) {
            $article->title = $data['title'];
        }
        if (isset($data['slug'])) {
            $article->slug = $this->generateSlug($data['slug']);
        }
        if (array_key_exists('description', $data)) {
            $article->description = $data['description'];
        }
        if (array_key_exists('content', $data)) {
            $article->content = $data['content'];
        }
        if (isset($data['tags'])) {
            $tags = $this->normalizeTags($data['tags']);
            $article->tags = $tags === [] ? null : $tags;
        }
        if (isset($data['is_published'])) {
            $article->is_published = filter_var($data['is_published'], FILTER_VALIDATE_BOOLEAN);
        }
        if (isset($data['sort_order'])) {
            $article->sort_order = $data['sort_order'];
        }
        if (isset($data['category_id'])) {
            if (empty($data['category_id'])) {
                throw new RuntimeException('Category not found');
            }

            $category = WikiCategory::findByPK($data['category_id']);
            if (!$category) {
                throw new RuntimeException('Category not found');
            }

            $article->category = $category;
            $article->sort_order = $data['sort_order'] ?? $this->getNextArticleSortOrder($category->id);
        }

        $article->updatedAt = new DateTimeImmutable();
        $article->saveOrFail();

        return $article;
    }

    public function deleteArticle(WikiArticle $article): bool
    {
        return $article->delete();
    }

    private function generateCategorySlug(string $name): string
    {
        $slug = mb_strtolower($name);
        $slug = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $slug);
        $slug = preg_replace('/[\s_]+/', '-', $slug);
        $slug = trim($slug, '-');

        $originalSlug = $slug;
        $counter = 1;
        while (WikiCategory::findOne(['slug' => $slug])) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function getNextCategorySortOrder(): int
    {
        $maxOrder = WikiCategory::query()
            ->orderBy('sort_order', 'DESC')
            ->fetchOne();

        return $maxOrder ? $maxOrder->sort_order + 1 : 0;
    }

    private function generateSlug(string $title): string
    {
        $slug = mb_strtolower($title);
        $slug = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $slug);
        $slug = preg_replace('/[\s_]+/', '-', $slug);
        $slug = trim($slug, '-');

        $originalSlug = $slug;
        $counter = 1;
        while (WikiArticle::findOne(['slug' => $slug])) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function getNextArticleSortOrder(int $categoryId): int
    {
        $maxOrder = WikiArticle::query()
            ->where('category_id', $categoryId)
            ->orderBy('sort_order', 'DESC')
            ->fetchOne();

        return $maxOrder ? $maxOrder->sort_order + 1 : 0;
    }

    /**
     * Normalize tags input into an array suitable for JSON storage.
     */
    private function normalizeTags(array|string|null $raw): string
    {
        if (is_string($raw)) {
            $raw = explode(',', $raw);
        }

        if (!is_array($raw)) {
            return json_encode([]);
        }

        $tags = array_map('trim', $raw);
        $tags = array_filter($tags, static fn ($tag) => $tag !== '');

        return json_encode(array_values($tags));
    }
}
