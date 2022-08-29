<?php
namespace Apie\HtmlBuilders\TestHelpers;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Context\ApieContext;
use Apie\Fixtures\BoundedContextFactory;
use Apie\HtmlBuilders\Components\Dashboard\RawContents;
use Apie\HtmlBuilders\Components\Layout;
use Apie\HtmlBuilders\Components\Layout\BoundedContextSelect;
use Apie\HtmlBuilders\Components\Layout\LoginSelect;
use Apie\HtmlBuilders\Components\Layout\Logo;
use Apie\HtmlBuilders\Configuration\CurrentConfiguration;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\ComponentRendererInterface;
use Generator;
use PHPUnit\Framework\TestCase;

/**
 * @codeCoverageIgnore
 */
abstract class AbstractRenderTest extends TestCase
{
    abstract public function getRenderer(): ComponentRendererInterface;

    abstract public function getFixturesPath(): string;

    /**
     * Overwriting this method to return true means the fixtures will be overwritten to ease big refactorings.
     */
    protected function shouldOverwriteFixture(): bool
    {
        return false;
    }

    /**
     * @dataProvider provideComponents
     */
    public function testRender(string $expectedFixtureFile, ComponentInterface $component)
    {
        $renderer = $this->getRenderer();
        $actual = $renderer->render($component);
        $fixtureFile = $this->getFixturesPath() . DIRECTORY_SEPARATOR . $expectedFixtureFile;
        if ($this->shouldOverwriteFixture()) {
            file_put_contents($fixtureFile, $actual);
        }
        $expected = file_get_contents($fixtureFile);
        $this->assertEquals($expected, $actual);
    }

    public function provideComponents(): Generator
    {
        $rawContents = new RawContents('<marquee>Hello world</marquee>');
        $defaultConfiguration = new CurrentConfiguration([], new ApieContext(), BoundedContextFactory::createHashmap(), new BoundedContextId('default'));
        yield 'Raw HTML concents' => [
            'expected-raw-contents.html',
            $rawContents,
        ];
        yield 'Simple layout' => [
            'expected-simple-layout.html',
            new Layout(
                'Title',
                $defaultConfiguration,
                $rawContents
            )
        ];
        yield 'Bounded context select => nothing selected' => [
            'expected-bounded-context-select-nothing.html',
            new BoundedContextSelect(
                new CurrentConfiguration([], new ApieContext(), BoundedContextFactory::createHashmap(), null)
            )
        ];
        yield 'Bounded context select => unknown selection' => [
            'expected-bounded-context-select-unknown.html',
            new BoundedContextSelect(
                new CurrentConfiguration([], new ApieContext(), BoundedContextFactory::createHashmap(), new BoundedContextId('unknown'))
            )
        ];
        yield 'Bounded context select => single bounded context' => [
            'expected-bounded-context-select.html',
            new BoundedContextSelect(
                $defaultConfiguration
            )
        ];
        yield 'Bounded context select => multiple bounded context' => [
            'expected-bounded-context-select-multiple.html',
            new BoundedContextSelect(
                new CurrentConfiguration([], new ApieContext(), BoundedContextFactory::createHashmapWithMultipleContexts(), new BoundedContextId('default'))
            )
        ];
        yield 'Logo' => [
            'expected-logo.html',
            new Logo(),
        ];
        yield 'Login select' => [
            'expected-login-select.html',
            new LoginSelect(
                $defaultConfiguration
            )
        ];

        yield 'Simple Menu' => [
            'expected-menu.html',
            new Layout\Menu($defaultConfiguration),
        ];
    }
}
