<?php
namespace Apie\Tests\HtmlBuilders\TestHelpers;

use Apie\Core\Context\ApieContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Interfaces\ComponentRendererInterface;
use Apie\HtmlBuilders\TestHelpers\AbstractRenderTestCase;

class AbstractRenderTestCaseTest extends AbstractRenderTestCase
{
    public function getRenderer(): ComponentRendererInterface
    {
        return new class implements ComponentRendererInterface {
            public function render(ComponentInterface $componentInterface, ApieContext $apieContext): string
            {
                return get_debug_type($componentInterface);
            }
        };
    }

    public function getFixturesPath(): string
    {
        return __DIR__ . '/../../fixtures';
    }

    /**
     * @test
     */
    public function it_provides_a_list_of_components()
    {
        $list = iterator_to_array($this->provideComponents());
        $this->assertNotEmpty($list);
        foreach ($list as $item) {
            $this->assertArrayHasKey(0, $item);
            $this->assertArrayHasKey(1, $item);
            $this->assertIsString($item[0]);
            $this->assertInstanceOf(ComponentInterface::class, $item[1]);
        }
    }
}
