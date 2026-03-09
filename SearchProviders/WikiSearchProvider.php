<?php

namespace Flute\Modules\Wiki\SearchProviders;

use Flute\Modules\Search\Contracts\SearchProviderInterface;
use Flute\Modules\Search\DTO\SearchQuery;
use Flute\Modules\Search\DTO\SearchResult;
use Flute\Modules\Wiki\database\Entities\WikiArticle;
use Illuminate\Support\Str;

class WikiSearchProvider implements SearchProviderInterface
{
    public function getKey(): string
    {
        return 'wiki';
    }

    public function getTitle(): string
    {
        return __('wiki.title');
    }

    public function getDescription(): string
    {
        return __('wiki.search_provider_desc');
    }

    public function getIcon(): string
    {
        return 'ph.bold.book-open-bold';
    }

    public function isEnabledByDefault(): bool
    {
        return true;
    }

    public function search(SearchQuery $query): array
    {
        $search = $query->value;
        $searchLower = $query->valueLower;

        if ($search === '') {
            return [];
        }

        $like = '%' . $this->escapeLike($search) . '%';

        $articles = WikiArticle::query()
            ->where('is_published', true)
            ->where(static function ($q) use ($like) {
                $q->where('title', 'LIKE', $like)
                    ->orWhere('description', 'LIKE', $like)
                    ->orWhere('content', 'LIKE', $like);
            })
            ->load('category')
            ->orderBy('views', 'DESC')
            ->limit(min(20, $query->limit))
            ->fetchAll();

        $results = [];

        foreach ($articles as $article) {
            $titleLower = mb_strtolower($article->title ?? '', 'UTF-8');

            $relevance = 1;
            if ($titleLower === $searchLower) {
                $relevance = 4;
            } elseif (str_starts_with($titleLower, $searchLower)) {
                $relevance = 3;
            } elseif (mb_strpos($titleLower, $searchLower) !== false) {
                $relevance = 2;
            }

            $subtitle = $article->category?->name ?? null;
            $desc = $article->description ? Str::limit(strip_tags($article->description), 120) : null;

            $results[] = new SearchResult(
                provider: $this->getKey(),
                providerTitle: $this->getTitle(),
                id: $article->id,
                title: $article->title,
                url: (string) url('/wiki/article/' . $article->slug),
                subtitle: $subtitle,
                icon: 'ph.bold.book-open-bold',
                image: null,
                relevance: $relevance,
                meta: [
                    'description' => $desc,
                    'slug' => $article->slug,
                    'category' => $article->category?->name,
                ]
            );
        }

        return $results;
    }

    private function escapeLike(string $value): string
    {
        return str_replace(['%', '_', '\\'], ['\\%', '\\_', '\\\\'], $value);
    }
}
