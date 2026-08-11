<?php
namespace Apie\Tests\HtmlBuilders\ErrorHandler;

use Apie\Core\Context\ApieContext;
use Apie\Fixtures\TestHelpers\TestWithFaker;
use Apie\Fixtures\TestHelpers\TestWithOpenapiSchema;
use Apie\HtmlBuilders\ErrorHandler\WrappedErrorTrace;
use Apie\Serializer\Serializer;
use cebe\openapi\spec\Schema;
use PHPUnit\Framework\Attributes\Test;

class WrappedErrorTraceTest extends \PHPUnit\Framework\TestCase
{
    use TestWithOpenapiSchema;
    use TestWithFaker;
    #[Test]
    public function it_can_parse_an_stacktrace_line()
    {
        $testItem = WrappedErrorTrace::fromNative([]);
        $this->assertEquals([], $testItem->toNative());
    }

    #[Test]
    public function it_works_with_serializer()
    {
        $serializer = Serializer::create();
        $testItem = $serializer->denormalizeNewObject([], WrappedErrorTrace::class, new ApieContext());

        $this->assertEquals([], $testItem->toNative());
    }

    #[Test]
    public function it_works_with_schema_generator()
    {
        $this->runOpenapiSchemaTestForCreation(
            WrappedErrorTrace::class,
            'WrappedErrorTrace-post',
            [
                'type' => 'object',
                'properties' => [
                    'file' => new Schema(['type' => 'string', 'nullable'=> false]),
                    'line' => new Schema(['type' => 'integer', 'nullable'=> false]),
                    'function' => new Schema(['type' => 'string', 'nullable'=> false]),
                    'class' => new Schema(['type' => 'string', 'nullable'=> false]),
                    'type' => new Schema(['type' => 'string', 'nullable'=> false]),
                ],
            ]
        );
    }

    #[Test]
    public function it_works_with_apie_faker()
    {
        $this->runFakerTest(WrappedErrorTrace::class);
    }
}
