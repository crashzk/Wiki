<?php

namespace Flute\Modules\Wiki\Controllers;

use Flute\Core\Router\Annotations\Route;
use Flute\Core\Support\BaseController;
use Flute\Modules\Wiki\Services\WikiServiceInterface;

class WikiController extends BaseController
{
    protected WikiServiceInterface $wikiService;

    public function __construct(WikiServiceInterface $wikiService)
    {
        $this->wikiService = $wikiService;
    }

    #[Route('/wiki', name: 'wiki.index', methods: ['GET'])]
    public function index()
    {
        $categories = $this->wikiService->getActiveCategories();
        $popularArticles = $this->wikiService->getPopularArticles(8);
        $totalArticles = $this->wikiService->getTotalArticlesCount();

        return view('wiki::home', [
            'categories' => $categories,
            'popularArticles' => $popularArticles,
            'totalArticles' => $totalArticles,
        ]);
    }

    #[Route('/wiki/article/{slug}', name: 'wiki.article', methods: ['GET'])]
    public function article(string $slug)
    {
        $article = $this->wikiService->getArticleBySlug($slug);

        if (!$article) {
            return $this->error(__('wiki.article_not_found'), 404);
        }

        $this->wikiService->incrementViews($article);

        $categories = $this->wikiService->getActiveCategories();
        $relatedArticles = $this->wikiService->getRelatedArticles($article);

        return view('wiki::article', [
            'categories' => $categories,
            'article' => $article,
            'relatedArticles' => $relatedArticles,
        ]);
    }

    #[Route('/wiki/search', name: 'wiki.search', methods: ['GET'])]
    public function search()
    {
        $query = request()->input('q', '');
        $results = [];

        if (!empty($query)) {
            $results = $this->wikiService->searchArticles($query);
        }

        return response()->json([
            'results' => array_map(static fn ($article) => [
                'id' => $article->id,
                'title' => $article->title,
                'description' => $article->description,
                'category' => $article->category->name,
                'url' => route('wiki.article', ['slug' => $article->slug]),
            ], $results),
        ]);
    }

    #[Route('/wiki/article/{slug}/feedback', name: 'wiki.feedback', methods: ['POST'])]
    public function feedback(string $slug)
    {
        $article = $this->wikiService->getArticleBySlug($slug);

        if (!$article) {
            return response()->json(['error' => __('wiki.article_not_found')], 404);
        }

        $helpful = request()->input('helpful');
        $isHelpful = filter_var($helpful, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? ($helpful === 1 || $helpful === '1');
        $this->wikiService->addFeedback($article, $isHelpful);

        return response()->json([
            'success' => true,
            'helpful' => $article->helpful,
            'not_helpful' => $article->not_helpful,
        ]);
    }
}
