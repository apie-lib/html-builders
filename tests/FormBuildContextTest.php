<?php
namespace Apie\Tests\HtmlBuilders;

use Apie\Core\Context\ApieContext;
use Apie\Core\ContextConstants;
use Apie\HtmlBuilders\Factories\FormComponentFactory;
use Apie\HtmlBuilders\FormBuildContext;
use PHPUnit\Framework\TestCase;

class FormBuildContextTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_be_constructed()
    {
        $factory = FormComponentFactory::create();
        $apieContext = new ApieContext([
            ContextConstants::VALIDATION_ERRORS => [
                'test' => 'this is an error',
                'test2' => [
                    '' => 'global error',
                    'a' => 'this is an other error',
                ]
            ]
        ]);
        $testItem = new FormBuildContext(
            $factory,
            $apieContext,
            [
                'test' => 12,
            ],
            false
        );
        $this->assertFalse($testItem->isMultipart());
        $this->assertSame($apieContext, $testItem->getApieContext());
        $this->assertSame($factory, $testItem->getComponentFactory());

        $this->assertSame(
            [
                'test' => 12,
            ],
            $testItem->getFilledInValue()
        );
        $this->assertNull(
            $testItem->getValidationError()
        );

        return $testItem;
    }

    #[\PHPUnit\Framework\Attributes\DependsUsingDeepClone('it_can_be_constructed')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_change_context_immutable(FormBuildContext $testItem)
    {
        $apieContext = $testItem->getApieContext();
        $clone = $testItem->withApieContext('test', 1);
        $this->assertSame($apieContext, $testItem->getApieContext());
        $this->assertEquals(1, $clone->getApieContext()->getContext('test'));
    }
}
