<?php

namespace Flute\Modules\Wiki\Providers;

use Flute\Core\Support\ModuleServiceProvider;
use Flute\Modules\Search\Services\SearchRegistry;
use Flute\Modules\Wiki\Admin\Package\WikiPackage;
use Flute\Modules\Wiki\SearchProviders\WikiSearchProvider;
use Flute\Modules\Wiki\Services\WikiService;
use Flute\Modules\Wiki\Services\WikiServiceInterface;

class WikiProvider extends ModuleServiceProvider
{
    public array $extensions = [];

    public function boot(\DI\Container $container): void
    {
        $this->bootstrapModule();

        $container->set(WikiServiceInterface::class, \DI\get(WikiService::class));
        $container->set(WikiService::class, \DI\autowire(WikiService::class));

        $this->loadViews('Resources/views', 'wiki');
        $this->loadScss('Resources/assets/scss/wiki.scss');

        $this->loadPackage(new WikiPackage());

        if ($container->has(SearchRegistry::class)) {
            $container->get(SearchRegistry::class)->registerProvider(new WikiSearchProvider());
        }
    }

    public function register(\DI\Container $container): void
    {
    }
}
