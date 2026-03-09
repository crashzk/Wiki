<?php

namespace Flute\Modules\Wiki\database\Entities;

use Cycle\ActiveRecord\ActiveRecord;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Cycle\Annotated\Annotation\Table\Index;
use DateTimeImmutable;
use Flute\Core\Database\Entities\User;

#[Entity(table: 'wiki_articles')]
#[Index(columns: ['slug'], unique: true)]
class WikiArticle extends ActiveRecord
{
    #[Column(type: 'primary')]
    public int $id;

    #[Column(type: 'string')]
    public string $title;

    #[Column(type: 'string', unique: true)]
    public string $slug;

    #[Column(type: 'text', nullable: true)]
    public ?string $description = null;

    #[Column(type: 'text', nullable: true)]
    public ?string $content = null;

    #[Column(type: 'json', nullable: true)]
    public array|string|null $tags = null;

    #[BelongsTo(target: WikiCategory::class, nullable: false, innerKey: 'category_id')]
    public WikiCategory $category;

    #[BelongsTo(target: User::class, nullable: true, innerKey: 'author_id')]
    public ?User $author = null;

    #[Column(type: 'integer', default: 0)]
    public int $views = 0;

    #[Column(type: 'integer', default: 0)]
    public int $helpful = 0;

    #[Column(type: 'integer', default: 0)]
    public int $not_helpful = 0;

    #[Column(type: 'boolean', default: true)]
    public bool $is_published = true;

    #[Column(type: 'integer', default: 0)]
    public int $sort_order = 0;

    #[Column(type: 'datetime')]
    public DateTimeImmutable $createdAt;

    #[Column(type: 'datetime')]
    public DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function incrementViews(): void
    {
        $this->views++;
        $this->saveOrFail();
    }

    public function addHelpful(): void
    {
        $this->helpful++;
        $this->saveOrFail();
    }

    public function addNotHelpful(): void
    {
        $this->not_helpful++;
        $this->saveOrFail();
    }

    public function getTagsArray(): array
    {
        if (is_array($this->tags)) {
            return $this->tags;
        }

        if (is_string($this->tags)) {
            $decoded = json_decode($this->tags, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return [];
    }

    public function setTagsFromString(string $tagsString): void
    {
        $tags = array_map('trim', explode(',', $tagsString));
        $tags = array_filter($tags, static fn ($tag) => $tag !== '');
        $this->tags = $tags === [] ? null : $tags;
    }
}
