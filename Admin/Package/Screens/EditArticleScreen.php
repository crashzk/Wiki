<?php

namespace Flute\Modules\Wiki\Admin\Package\Screens;

use Flute\Admin\Platform\Actions\Button;
use Flute\Admin\Platform\Fields\Input;
use Flute\Admin\Platform\Fields\RichText;
use Flute\Admin\Platform\Fields\Select;
use Flute\Admin\Platform\Fields\TextArea;
use Flute\Admin\Platform\Fields\Toggle;
use Flute\Admin\Platform\Layouts\LayoutFactory;
use Flute\Admin\Platform\Screen;
use Flute\Admin\Platform\Support\Color;
use Flute\Modules\Wiki\database\Entities\WikiArticle;
use Flute\Modules\Wiki\database\Entities\WikiCategory;
use Flute\Modules\Wiki\Services\WikiService;

class EditArticleScreen extends Screen
{
    public ?string $name = null;

    public ?string $description = null;

    public ?string $permission = 'admin.wiki';

    public ?WikiArticle $article = null;

    protected WikiService $wikiService;

    public function mount(): void
    {
        $this->wikiService = app(WikiService::class);

        $id = request()->input('id');
        if ($id) {
            $this->article = WikiArticle::query()
                ->where('id', (int) $id)
                ->load('category')
                ->fetchOne();

            if (!$this->article) {
                $this->flashMessage(__('wiki.admin.messages.article_not_found'), 'error');
                $this->redirect('/admin/wiki/articles');

                return;
            }
        }

        $this->name = $this->article
            ? __('wiki.admin.title.edit_article')
            : __('wiki.admin.title.add_article');

        breadcrumb()
            ->add(__('def.admin_panel'), url('/admin'))
            ->add(__('wiki.admin.title.articles'), url('/admin/wiki/articles'))
            ->add($this->name);
    }

    public function commandBar(): array
    {
        return [
            Button::make(__('def.save'))
                ->type(Color::PRIMARY)
                ->icon('ph.bold.floppy-disk-bold')
                ->method('save'),

            Button::make(__('def.cancel'))
                ->type(Color::OUTLINE_SECONDARY)
                ->redirect(url('/admin/wiki/articles')),
        ];
    }

    public function layout(): array
    {
        return [
            LayoutFactory::split([
                LayoutFactory::block([
                    LayoutFactory::field(
                        Input::make('title')
                            ->value(request()->input('title', $this->article?->title))
                            ->placeholder(__('wiki.admin.fields.title_placeholder'))
                    )
                        ->label(__('wiki.admin.fields.title'))
                        ->required(),

                    LayoutFactory::field(
                        Select::make('category_id')
                            ->options($this->getCategoryOptions())
                            ->value(request()->input('category_id', $this->article?->category?->id))
                    )
                        ->label(__('wiki.admin.fields.category'))
                        ->required(),

                    LayoutFactory::field(
                        TextArea::make('description')
                            ->value(request()->input('description', $this->article?->description))
                            ->rows(2)
                    )
                        ->label(__('wiki.admin.fields.description'))
                        ->small(__('wiki.admin.fields.description_help')),

                    LayoutFactory::field(
                        Input::make('tags')
                            ->value(request()->input('tags', $this->article ? implode(', ', $this->article->getTagsArray()) : ''))
                            ->placeholder(__('wiki.admin.fields.tags_placeholder'))
                    )
                        ->label(__('wiki.admin.fields.tags'))
                        ->small(__('wiki.admin.fields.tags_help')),
                ])->title(__('wiki.admin.sections.article_info')),

                LayoutFactory::block([
                    LayoutFactory::field(
                        Toggle::make('is_published')
                            ->checked(filter_var(request()->input('is_published', $this->article?->is_published ?? true), FILTER_VALIDATE_BOOLEAN))
                    )
                        ->label(__('wiki.admin.fields.published')),
                ])->title(__('wiki.admin.sections.settings')),
            ])->ratio('60/40'),

            LayoutFactory::block([
                LayoutFactory::field(
                    RichText::make('content')
                        ->value(request()->input('content', $this->article?->content ?? ''))
                        ->enableImageUpload(true)
                        ->imageUploadEndpoint(url('/admin/wiki/articles/upload-image'))
                )
                    ->label(__('wiki.admin.fields.content')),
            ])->title(__('wiki.admin.sections.article_content')),
        ];
    }

    public function save(): void
    {
        $data = request()->input();

        if (empty($data['title']) || empty($data['category_id'])) {
            $this->flashMessage(__('wiki.admin.messages.required_fields'), 'error');

            return;
        }

        $tags = [];
        if (!empty($data['tags'])) {
            $tags = array_map('trim', explode(',', $data['tags']));
            $tags = array_filter($tags);
        }

        $articleData = [
            'title' => $data['title'],
            'category_id' => (int) $data['category_id'],
            'description' => $data['description'] ?? null,
            'content' => $data['content'] ?? null,
            'tags' => $tags,
            'is_published' => isset($data['is_published']),
        ];

        if ($this->article) {
            $this->wikiService->updateArticle($this->article, $articleData);
            $this->flashMessage(__('wiki.admin.messages.article_updated'), 'success');
        } else {
            $articleData['author_id'] = user()->getCurrentUser()?->id;
            $this->wikiService->createArticle($articleData);
            $this->flashMessage(__('wiki.admin.messages.article_created'), 'success');
        }

        $this->redirectTo('/admin/wiki/articles', 300);
    }

    private function getCategoryOptions(): array
    {
        $categories = WikiCategory::query()
            ->where('active', true)
            ->orderBy('sort_order', 'ASC')
            ->fetchAll();

        $options = [];
        foreach ($categories as $cat) {
            $options[$cat->id] = $cat->name;
        }

        return $options;
    }
}
