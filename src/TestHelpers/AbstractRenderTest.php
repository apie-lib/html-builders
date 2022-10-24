<?php
namespace Apie\HtmlBuilders\TestHelpers;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Context\ApieContext;
use Apie\Core\Enums\RequestMethod;
use Apie\Fixtures\BoundedContextFactory;
use Apie\HtmlBuilders\Components\Dashboard\RawContents;
use Apie\HtmlBuilders\Components\Forms\Csrf;
use Apie\HtmlBuilders\Components\Forms\Form;
use Apie\HtmlBuilders\Components\Forms\FormPrototypeList;
use Apie\HtmlBuilders\Components\Forms\FormSplit;
use Apie\HtmlBuilders\Components\Forms\HiddenField;
use Apie\HtmlBuilders\Components\Forms\Input;
use Apie\HtmlBuilders\Components\Forms\Password;
use Apie\HtmlBuilders\Components\Layout;
use Apie\HtmlBuilders\Components\Layout\BoundedContextSelect;
use Apie\HtmlBuilders\Components\Layout\LoginSelect;
use Apie\HtmlBuilders\Components\Layout\Logo;
use Apie\HtmlBuilders\Components\Resource\Overview;
use Apie\HtmlBuilders\Configuration\CurrentConfiguration;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\ComponentRendererInterface;
use Apie\HtmlBuilders\Lists\ComponentHashmap;
use Apie\HtmlBuilders\ValueObjects\FormName;
use Apie\TextValueObjects\StrongPassword;
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
        return true;//false;
    }

    /**
     * @dataProvider provideComponents
     */
    public function testRender(string $expectedFixtureFile, ComponentInterface $component): void
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
            new Logo($defaultConfiguration),
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

        yield 'Resource overview' => [
            'expected-resource-overview.html',
            new Overview([['id' => 12, 'name' => 'Pizza']], ['id', 'name'])
        ];

        yield 'Resource overview large list' => [
            'expected-resource-overview-large-list.html',
            new Overview(array_fill(0, 100, ['id' => 12, 'name' => 'Pizza']), ['id', 'name'])
        ];

        yield 'Form' => [
            'expected-form.html',
            new Form(RequestMethod::POST, new RawContents('test'), new RawContents('test2')),
        ];

        yield 'Simple input field' => [
            'expected-input.html',
            new Input('name', 'value')
        ];

        yield 'Simple password field' => [
            'expected-input-password.html',
            new Input('name', 'value', 'password')
        ];
        yield 'Hidden field' => [
            'expected-hidden-field.html',
            new HiddenField('name', 'value')
        ];
        yield 'Password field' => [
            'expected-password-field.html',
            new Password(StrongPassword::class, new FormName('name'), 'value')
        ];

        yield 'Union type' => [
            'expected-type-split.html',
            new FormSplit(
                new FormName('name'),
                '42',
                new ComponentHashmap([
                    'input' => new Input('name', 'value'),
                    'password' => new Input('name', 'value', 'password')
                ])
            )
        ];

        yield 'Form list' => [
            'expected-form-list.html',
            new FormPrototypeList(
                new FormName('name'),
                [],
                new Input('name', 'value', 'tel')
            )
        ];
        yield 'CSRF token' => [
            'expected-csrf-token.html',
            new Csrf('token-123')
        ];
    }
}
