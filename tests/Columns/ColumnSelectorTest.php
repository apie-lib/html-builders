<?php
namespace Apie\Tests\HtmlBuilders\Columns;

use Apie\Core\Context\ApieContext;
use Apie\Fixtures\Entities\Order;
use Apie\Fixtures\Entities\Polymorphic\Animal;
use Apie\Fixtures\Entities\Polymorphic\Cow;
use Apie\HtmlBuilders\Columns\ColumnSelector;
use Generator;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ColumnSelectorTest extends TestCase
{
    /**
     * @test
     * @dataProvider classProvider
     */
    public function it_can_retrieve_columns_from_an_entity(array $expected, string $class)
    {
        $testItem = new ColumnSelector();
        $this->assertEquals($expected, $testItem->getColumns(new ReflectionClass($class), new ApieContext()));
    }

    public function classProvider(): Generator
    {
        yield 'Regular entity' => [
            ['id', 'orderStatus', 'orderLines'],
            Order::class
        ];
        yield 'Polymorphic entity base class' => [
            ['id', 'animalType', 'hasMilk', 'starving', 'poisonous'],
            Animal::class
        ];
        yield 'Polymorphic entity child class' => [
            ['id', 'animalType', 'hasMilk'],
            Cow::class
        ];
    }
}
