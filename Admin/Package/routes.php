<?php

use Flute\Core\Router\Router;
use Flute\Modules\Wiki\Admin\Package\Screens\ArticleListScreen;
use Flute\Modules\Wiki\Admin\Package\Screens\CategoryListScreen;
use Flute\Modules\Wiki\Admin\Package\Screens\EditArticleScreen;
use Flute\Modules\Wiki\Admin\Package\Screens\EditCategoryScreen;

Router::screen('/admin/wiki/categories', CategoryListScreen::class);
Router::screen('/admin/wiki/categories/add', EditCategoryScreen::class);
Router::screen('/admin/wiki/categories/{id}/edit', EditCategoryScreen::class);

Router::screen('/admin/wiki/articles', ArticleListScreen::class);
Router::screen('/admin/wiki/articles/add', EditArticleScreen::class);
Router::screen('/admin/wiki/articles/{id}/edit', EditArticleScreen::class);
