<?php
namespace Apie\Tests\HtmlBuilders\Factories;

use Apie\Common\ActionDefinitionProvider;
use Apie\Core\BoundedContext\BoundedContextHashmap;
use Apie\Core\Context\ApieContext;
use Apie\HtmlBuilders\Components\Dashboard\RawContents;
use Apie\HtmlBuilders\Components\Layout;
use Apie\HtmlBuilders\Configuration\ApplicationConfiguration;
use Apie\HtmlBuilders\Factories\ComponentFactory;
use Apie\HtmlBuilders\Factories\FieldDisplayComponentFactory;
use Apie\HtmlBuilders\Factories\FormComponentFactory;
use Apie\HtmlBuilders\Factories\ResourceActionFactory;
use PHPUnit\Framework\TestCase;

class ComponentFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_create_a_layout_component()
    {
        $testItem = new ComponentFactory(
            new ApplicationConfiguration([]),
            new BoundedContextHashmap([]),
            FormComponentFactory::create(),
            FieldDisplayComponentFactory::create([]),
            new ResourceActionFactory(new ActionDefinitionProvider)
        );
        $this->assertInstanceOf(
            Layout::class,
            $testItem->createWrapLayout('Page title', null, new ApieContext([]), new RawContents(''))
        );
    }
}
