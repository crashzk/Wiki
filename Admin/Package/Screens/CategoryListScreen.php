<?php

namespace Flute\Modules\Wiki\Admin\Package\Screens;

use Flute\Admin\Platform\Actions\Button;
use Flute\Admin\Platform\Actions\DropDown;
use Flute\Admin\Platform\Actions\DropDownItem;
use Flute\Admin\Platform\Fields\TD;
use Flute\Admin\Platform\Layouts\LayoutFactory;
use Flute\Admin\Platform\Screen;
use Flute\Admin\Platform\Support\Color;
use Flute\Modules\Wiki\database\Entities\WikiCategory;

class CategoryListScreen extends Screen
{
    public ?string $name = 'wiki.admin.title.categories';

    public ?string $description = 'wiki.admin.title.categories_description';

    public ?string $permission = 'admin.wiki';

    public $categories;

    public function mount(): void
    {
        breadcrumb()
            ->add(__('def.admin_panel'), url('/admin'))
            ->add(__('wiki.admin.title.categories'));

        $this->categories = WikiCategory::query()
            ->load('articles')
            ->orderBy('sort_order', 'ASC');
    }

    public function commandBar(): array
    {
        return [
            Button::make(__('wiki.admin.buttons.add_category'))
                ->type(Color::PRIMARY)
                ->icon('ph.bold.plus-bold')
                ->redirect(url('/admin/wiki/categories/add')),
        ];
    }

    public function layout(): array
    {
        return [
            LayoutFactory::table('categories', [
                TD::make('name', __('wiki.admin.fields.name'))
                    ->width('250px')
                    ->render(static function (WikiCategory $category) {
                        $icon = $category->icon ? "<i class=\"{$category->icon}\"></i>" : '';

                        return "<div style=\"display: flex; align-items: center; gap: 8px;\">{$icon}<strong>{$category->name}</strong></div>";
                    })
                    ->cantHide(),

                TD::make('slug', __('wiki.admin.fields.slug'))
                    ->width('150px'),

                TD::make('articles_count', __('wiki.admin.fields.articles_count'))
                    ->width('120px')
                    ->align(TD::ALIGN_CENTER)
                    ->render(static fn (WikiCategory $category) => count($category->articles)),

                TD::make('active', __('wiki.admin.fields.active'))
                    ->width('100px')
                    ->align(TD::ALIGN_CENTER)
                    ->render(static fn (WikiCategory $category) => $category->active
                        ? '<span class="badge success">' . __('def.yes') . '</span>'
                        : '<span class="badge error">' . __('def.no') . '</span>'),

                TD::make(__('def.actions'))
                    ->class('actions-col')
                    ->align(TD::ALIGN_CENTER)
                    ->disableSearch()
                    ->width('100px')
                    ->cantHide()
                    ->render(static fn (WikiCategory $category) => DropDown::make()
                        ->icon('ph.regular.dots-three-outline-vertical')
                        ->list([
                            DropDownItem::make(__('def.edit'))
                                ->type(Color::OUTLINE_PRIMARY)
                                ->icon('ph.regular.pencil')
                                ->size('small')
                                ->fullWidth()
                                ->redirect(url('admin/wiki/categories/' . $category->id . '/edit')),

                            DropDownItem::make(__('def.delete'))
                                ->fullWidth()
                                ->confirm(__('wiki.admin.confirms.delete_category'))
                                ->type(Color::OUTLINE_DANGER)
                                ->icon('ph.regular.trash')
                                ->size('small')
                                ->method('deleteCategory', [
                                    'id' => $category->id,
                                ]),
                        ])),
            ])->perPage(15)->searchable(['name', 'slug']),
        ];
    }

    public function deleteCategory(): void
    {
        $id = (int) request()->input('id');
        $category = WikiCategory::findByPK($id);

        if (!$category) {
            $this->flashMessage(__('wiki.admin.messages.category_not_found'), 'error');

            return;
        }

        foreach ($category->articles as $article) {
            $article->delete();
        }

        $category->delete();

        $this->flashMessage(__('wiki.admin.messages.category_deleted'), 'success');
        $this->redirectTo('/admin/wiki/categories');
    }
}
