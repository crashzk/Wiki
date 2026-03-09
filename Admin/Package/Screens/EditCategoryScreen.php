<?php

namespace Flute\Modules\Wiki\Admin\Package\Screens;

use Flute\Admin\Platform\Actions\Button;
use Flute\Admin\Platform\Fields\Input;
use Flute\Admin\Platform\Fields\TextArea;
use Flute\Admin\Platform\Fields\Toggle;
use Flute\Admin\Platform\Layouts\LayoutFactory;
use Flute\Admin\Platform\Screen;
use Flute\Admin\Platform\Support\Color;
use Flute\Modules\Wiki\database\Entities\WikiCategory;
use Flute\Modules\Wiki\Services\WikiService;

class EditCategoryScreen extends Screen
{
    public ?string $name = null;

    public ?string $description = null;

    public ?string $permission = 'admin.wiki';

    public ?WikiCategory $category = null;

    protected WikiService $wikiService;

    public function mount(): void
    {
        $this->wikiService = app(WikiService::class);

        $id = request()->input('id');
        if ($id) {
            $this->category = WikiCategory::findByPK((int) $id);
            if (!$this->category) {
                $this->flashMessage(__('wiki.admin.messages.category_not_found'), 'error');
                $this->redirect('/admin/wiki/categories');

                return;
            }
        }

        $this->name = $this->category
            ? __('wiki.admin.title.edit_category')
            : __('wiki.admin.title.add_category');

        breadcrumb()
            ->add(__('def.admin_panel'), url('/admin'))
            ->add(__('wiki.admin.title.categories'), url('/admin/wiki/categories'))
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
                ->redirect(url('/admin/wiki/categories')),
        ];
    }

    public function layout(): array
    {
        return [
            LayoutFactory::block([
                LayoutFactory::columns([
                    LayoutFactory::field(
                        Input::make('name')
                            ->value(request()->input('name', $this->category?->name))
                            ->placeholder(__('wiki.admin.fields.name_placeholder'))
                    )
                        ->label(__('wiki.admin.fields.name'))
                        ->required(),


                    LayoutFactory::field(
                        Input::make('icon')
                            ->type('icon')
                            ->value(request()->input('icon', $this->category?->icon))
                            ->placeholder('ph.regular.folder')
                    )
                        ->label(__('wiki.admin.fields.icon'))
                        ->small(__('wiki.admin.fields.icon_help')),
                ]),
                LayoutFactory::columns([

                    LayoutFactory::field(
                        TextArea::make('description')
                            ->value(request()->input('description', $this->category?->description))
                            ->rows(3)
                    )
                        ->label(__('wiki.admin.fields.description')),
                    LayoutFactory::field(
                        Toggle::make('active')
                            ->checked(filter_var(request()->input('active', $this->category?->active ?? true), FILTER_VALIDATE_BOOLEAN))
                    )
                        ->label(__('wiki.admin.fields.active')),
                ]),
            ]),
        ];
    }

    public function save(): void
    {
        $data = request()->input();

        if (empty($data['name'])) {
            $this->flashMessage(__('wiki.admin.messages.required_fields'), 'error');

            return;
        }

        $categoryData = [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'icon' => $data['icon'] ?? null,
            'active' => isset($data['active']),
        ];

        if ($this->category) {
            $this->wikiService->updateCategory($this->category, $categoryData);
            $this->flashMessage(__('wiki.admin.messages.category_updated'), 'success');
        } else {
            $this->wikiService->createCategory($categoryData);
            $this->flashMessage(__('wiki.admin.messages.category_created'), 'success');
        }

        $this->redirectTo('/admin/wiki/categories', 300);
    }
}
