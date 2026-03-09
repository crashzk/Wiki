<?php

namespace Flute\Modules\Wiki\Admin\Package;

use Flute\Admin\Support\AbstractAdminPackage;

class WikiPackage extends AbstractAdminPackage
{
    public function initialize(): void
    {
        parent::initialize();

        $this->loadRoutesFromFile('routes.php');
        $this->loadTranslations('Resources/lang');
    }

    public function getPermissions(): array
    {
        return ['admin', 'admin.wiki'];
    }

    public function getMenuItems(): array
    {
        return [
            [
                'title' => __('wiki.admin.menu.wiki'),
                'icon' => 'ph.bold.book-open-bold',
                'permission' => 'admin.wiki',
                'children' => [
                    [
                        'icon' => 'ph.bold.folder-bold',
                        'title' => __('wiki.admin.menu.categories'),
                        'url' => url('/admin/wiki/categories'),
                    ],
                    [
                        'icon' => 'ph.bold.article-bold',
                        'title' => __('wiki.admin.menu.articles'),
                        'url' => url('/admin/wiki/articles'),
                    ],
                ],
            ],
        ];
    }

    public function getPriority(): int
    {
        return 103;
    }
}
