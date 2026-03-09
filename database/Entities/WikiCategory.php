<?php

namespace Flute\Modules\Wiki\database\Entities;

use Cycle\ActiveRecord\ActiveRecord;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\HasMany;
use Cycle\Annotated\Annotation\Table\Index;

#[Entity(table: 'wiki_categories')]
#[Index(columns: ['slug'], unique: true)]
class WikiCategory extends ActiveRecord
{
    #[Column(type: 'primary')]
    public int $id;

    #[Column(type: 'string')]
    public string $name;

    #[Column(type: 'string', unique: true)]
    public string $slug;

    #[Column(type: 'text', nullable: true)]
    public ?string $description = null;

    #[Column(type: 'string', nullable: true)]
    public ?string $icon = null;

    #[Column(type: 'boolean', default: true)]
    public bool $active = true;

    #[Column(type: 'integer', default: 0)]
    public int $sort_order = 0;

    #[HasMany(target: WikiArticle::class, outerKey: 'category_id')]
    public array $articles = [];

    public function getActiveArticles(): array
    {
        return WikiArticle::query()
            ->where('category_id', $this->id)
            ->where('is_published', true)
            ->orderBy('sort_order', 'ASC')
            ->fetchAll();
    }

    public function getArticleCount(): int
    {
        return WikiArticle::query()
            ->where('category_id', $this->id)
            ->where('is_published', true)
            ->count();
    }
}
