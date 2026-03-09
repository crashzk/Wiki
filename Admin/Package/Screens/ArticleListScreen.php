<?php

namespace Flute\Modules\Wiki\Admin\Package\Screens;

use Carbon\Carbon;
use Flute\Admin\Platform\Actions\Button;
use Flute\Admin\Platform\Actions\DropDown;
use Flute\Admin\Platform\Actions\DropDownItem;
use Flute\Admin\Platform\Fields\TD;
use Flute\Admin\Platform\Layouts\LayoutFactory;
use Flute\Admin\Platform\Screen;
use Flute\Admin\Platform\Support\Color;
use Flute\Modules\Wiki\database\Entities\WikiArticle;

class ArticleListScreen extends Screen
{
    public ?string $name = 'wiki.admin.title.articles';

    public ?string $description = 'wiki.admin.title.articles_description';

    public ?string $permission = 'admin.wiki';

    public $articles;

    public function mount(): void
    {
        breadcrumb()
            ->add(__('def.admin_panel'), url('/admin'))
            ->add(__('wiki.admin.title.articles'));

        $this->articles = WikiArticle::query()
            ->load('category')
            ->load('author')
            ->orderBy('createdAt', 'DESC');
    }

    public function commandBar(): array
    {
        return [
            Button::make(__('wiki.admin.buttons.add_article'))
                ->type(Color::PRIMARY)
                ->icon('ph.bold.plus-bold')
                ->redirect(url('/admin/wiki/articles/add')),
        ];
    }

    public function layout(): array
    {
        return [
            LayoutFactory::table('articles', [
                TD::make('title', __('wiki.admin.fields.title'))
                    ->width('300px')
                    ->render(static fn (WikiArticle $article) => "<strong>{$article->title}</strong>")
                    ->cantHide(),

                TD::make('category', __('wiki.admin.fields.category'))
                    ->width('150px')
                    ->render(static fn (WikiArticle $article) => $article->category?->name ?? '—'),

                TD::make('views', __('wiki.admin.fields.views'))
                    ->width('100px')
                    ->align(TD::ALIGN_CENTER)
                    ->sort(),

                TD::make('is_published', __('wiki.admin.fields.published'))
                    ->width('100px')
                    ->align(TD::ALIGN_CENTER)
                    ->render(static fn (WikiArticle $article) => $article->is_published
                        ? '<span class="badge success">' . __('def.yes') . '</span>'
                        : '<span class="badge error">' . __('def.no') . '</span>'),

                TD::make('updatedAt', __('wiki.admin.fields.updated'))
                    ->width('150px')
                    ->sort()
                    ->defaultSort(true, 'desc')
                    ->render(static fn (WikiArticle $article) => (new Carbon($article->updatedAt))->diffForHumans()),

                TD::make(__('def.actions'))
                    ->class('actions-col')
                    ->align(TD::ALIGN_CENTER)
                    ->disableSearch()
                    ->width('100px')
                    ->cantHide()
                    ->render(static fn (WikiArticle $article) => DropDown::make()
                        ->icon('ph.regular.dots-three-outline-vertical')
                        ->list([
                            DropDownItem::make(__('def.edit'))
                                ->type(Color::OUTLINE_PRIMARY)
                                ->icon('ph.regular.pencil')
                                ->size('small')
                                ->fullWidth()
                                ->redirect(url('admin/wiki/articles/' . $article->id . '/edit')),

                            DropDownItem::make(__('def.delete'))
                                ->fullWidth()
                                ->confirm(__('wiki.admin.confirms.delete_article'))
                                ->type(Color::OUTLINE_DANGER)
                                ->icon('ph.regular.trash')
                                ->size('small')
                                ->method('deleteArticle', [
                                    'id' => $article->id,
                                ]),
                        ])),
            ])->perPage(15)->searchable(['title', 'slug', 'description']),
        ];
    }

    public function deleteArticle(): void
    {
        $id = (int) request()->input('id');
        $article = WikiArticle::findByPK($id);

        if (!$article) {
            $this->flashMessage(__('wiki.admin.messages.article_not_found'), 'error');

            return;
        }

        $article->delete();

        $this->flashMessage(__('wiki.admin.messages.article_deleted'), 'success');
        $this->redirectTo('/admin/wiki/articles');
    }
}
