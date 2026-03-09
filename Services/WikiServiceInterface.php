<?php

namespace Flute\Modules\Wiki\Services;

use Flute\Modules\Wiki\database\Entities\WikiArticle;
use Flute\Modules\Wiki\database\Entities\WikiCategory;

interface WikiServiceInterface
{
    public function getAllCategories(): array;

    public function getActiveCategories(): array;

    public function getCategoryBySlug(string $slug): ?WikiCategory;

    public function getArticleBySlug(string $slug): ?WikiArticle;

    public function searchArticles(string $query): array;

    public function getRelatedArticles(WikiArticle $article, int $limit = 4): array;

    public function getPopularArticles(int $limit = 6): array;

    public function getRecentArticles(int $limit = 5): array;

    public function getTotalArticlesCount(): int;

    public function incrementViews(WikiArticle $article): void;

    public function addFeedback(WikiArticle $article, bool $isHelpful): void;
}
