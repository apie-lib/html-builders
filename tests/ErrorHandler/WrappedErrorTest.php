<?php
namespace Apie\Tests\HtmlBuilders\ErrorHandler;

use Apie\HtmlBuilders\ErrorHandler\WrappedError;
use Exception;
use PHPUnit\Framework\TestCase;

class WrappedErrorTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_be_converted_to_json()
    {
        $error = new Exception(
            'This is a test',
            42,
            new Exception(
                'This is the next test',
                0,
                new Exception(
                    'The last test'
                )
            )
        );
        $testItem = new WrappedError($error);
        $actual = json_decode(json_encode($testItem), true);
        $this->assertArrayHasKey('exceptions', $actual);
        $this->assertArrayHasKey('files', $actual);
        $this->assertCount(3, $actual['exceptions']);
        $this->assertArrayHasKey('message', $actual['exceptions'][0]);
        $this->assertEquals('This is a test', $actual['exceptions'][0]['message']);
        $this->assertArrayHasKey('message', $actual['exceptions'][1]);
        $this->assertEquals('This is the next test', $actual['exceptions'][1]['message']);
        $this->assertArrayHasKey('message', $actual['exceptions'][2]);
        $this->assertEquals('The last test', $actual['exceptions'][2]['message']);
    }
}
