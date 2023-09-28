<?php
namespace Apie\HtmlBuilders;

use Apie\ServiceProviderGenerator\UseGeneratedMethods;
use Illuminate\Support\ServiceProvider;

/**
 * This file is generated with apie/service-provider-generator from file: html_builders.yaml
 * @codecoverageIgnore
 */
class HtmlBuilderServiceProvider extends ServiceProvider
{
    use UseGeneratedMethods;

    public function register()
    {
        $this->app->singleton(
            \Apie\HtmlBuilders\Factories\ComponentFactory::class,
            function ($app) {
                return new \Apie\HtmlBuilders\Factories\ComponentFactory(
                    $app->make(\Apie\HtmlBuilders\Configuration\ApplicationConfiguration::class),
                    $app->make(\Apie\Core\BoundedContext\BoundedContextHashmap::class),
                    $app->make(\Apie\HtmlBuilders\Factories\FormComponentFactory::class)
                );
            }
        );
        $this->app->singleton(
            \Apie\HtmlBuilders\Factories\FormComponentFactory::class,
            function ($app) {
                return \Apie\HtmlBuilders\Factories\FormComponentFactory::create(
                    $this->getTaggedServicesIterator(\Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface::class)
                );
                
            }
        );
        $this->app->singleton(
            \Apie\HtmlBuilders\Factories\Concrete\DropdownOptionsComponentProvider::class,
            function ($app) {
                return new \Apie\HtmlBuilders\Factories\Concrete\DropdownOptionsComponentProvider(
                    $app->make(\Apie\HtmlBuilders\Configuration\ApplicationConfiguration::class)
                );
            }
        );
        \Apie\ServiceProviderGenerator\TagMap::register(
            $this->app,
            \Apie\HtmlBuilders\Factories\Concrete\DropdownOptionsComponentProvider::class,
            array (
              0 => 'Apie\\HtmlBuilders\\Interfaces\\FormComponentProviderInterface',
            )
        );
        $this->app->tag([\Apie\HtmlBuilders\Factories\Concrete\DropdownOptionsComponentProvider::class], \Apie\HtmlBuilders\Interfaces\FormComponentProviderInterface::class);
        $this->app->singleton(
            \Apie\HtmlBuilders\Configuration\ApplicationConfiguration::class,
            function ($app) {
                return new \Apie\HtmlBuilders\Configuration\ApplicationConfiguration(
                    array (
                  'base_url' => '%apie.cms.base_url%',
                )
                );
            }
        );
        $this->app->singleton(
            \Apie\HtmlBuilders\ErrorHandler\CmsErrorRenderer::class,
            function ($app) {
                return new \Apie\HtmlBuilders\ErrorHandler\CmsErrorRenderer(
                    $app->make(\Apie\HtmlBuilders\Factories\ComponentFactory::class),
                    $app->make(\Apie\HtmlBuilders\Interfaces\ComponentRendererInterface::class),
                    $app->make(\Apie\Common\Interfaces\DashboardContentFactoryInterface::class),
                    $this->parseArgument('%apie.cms.error_template%')
                );
            }
        );
        $this->app->singleton(
            \Apie\HtmlBuilders\Assets\AssetManager::class,
            function ($app) {
                return \Apie\HtmlBuilders\Assets\AssetManager::create(
                    $this->parseArgument('%apie.cms.asset_folders%')
                );
                
            }
        );
        $this->app->singleton(
            \Apie\HtmlBuilders\Interfaces\ComponentRendererInterface::class,
            function ($app) {
                return \Apie\Common\Wrappers\CmsRendererFactory::createRenderer(
                    $app->make(\Apie\HtmlBuilders\Assets\AssetManager::class)
                );
                
            }
        );
        
    }
}
