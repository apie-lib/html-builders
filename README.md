<img src="https://raw.githubusercontent.com/apie-lib/apie-lib-monorepo/main/docs/apie-logo.svg" width="100px" align="left" />
<h1>html-builders</h1>






 [![Latest Stable Version](https://poser.pugx.org/apie/html-builders/v)](https://packagist.org/packages/apie/html-builders) [![Total Downloads](https://poser.pugx.org/apie/html-builders/downloads)](https://packagist.org/packages/apie/html-builders) [![Latest Unstable Version](https://poser.pugx.org/apie/html-builders/v/unstable)](https://packagist.org/packages/apie/html-builders) [![License](https://poser.pugx.org/apie/html-builders/license)](https://packagist.org/packages/apie/html-builders) [![PHP Composer](https://apie-lib.github.io/projectCoverage/coverage-html-builders.svg)](https://apie-lib.github.io/projectCoverage/html-builders/index.html)  

[![PHP Composer](https://github.com/apie-lib/html-builders/actions/workflows/php.yml/badge.svg?event=push)](https://github.com/apie-lib/html-builders/actions/workflows/php.yml)

This package is part of the [Apie](https://github.com/apie-lib) library.
The code is maintained in a monorepo, so PR's need to be sent to the [monorepo](https://github.com/apie-lib/apie-lib-monorepo/pulls)

## Documentation
Reusable HTML components and builder contexts used by the Apie CMS and exporters: forms, resource listings/dashboards, field display providers, and error rendering.

### Standalone usage
Install it with:
```bash
composer require apie/html-builders
```

Build a component tree with the classes in `Apie\HtmlBuilders\Components` (e.g. `Layout`, and the `Forms`/`Resource`/`Dashboard` components), configured through `Apie\HtmlBuilders\Factories\ComponentFactory` and rendered via `Apie\HtmlBuilders\Interfaces\ComponentRendererInterface`. `Apie\HtmlBuilders\Columns\ColumnSelector` is also reused by `apie/export`. The component classes themselves are framework-free; only the renderer wiring needs a container.

### Symfony integration
Via `apie/apie-bundle`, `html_builders.yaml` is loaded automatically and registers the component/field-display factories, `ApplicationConfiguration`, `AssetManager`, and `CmsErrorRenderer`. Configuration keys such as `apie.cms.base_url`, `apie.cms.asset_folders` and `apie.cms.error_template` (under `config/packages/apie.yaml`) feed these services.

### Laravel integration
Via `apie/laravel-apie`, the generated `Apie\HtmlBuilders\HtmlBuilderServiceProvider` is auto-registered and wires the same component factories and configuration into the Laravel container.
