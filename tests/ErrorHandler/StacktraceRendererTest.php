<?php
namespace Apie\Tests\HtmlBuilders\ErrorHandler;

use Apie\HtmlBuilders\ErrorHandler\StacktraceRenderer;
use PHPUnit\Framework\TestCase;

class StacktraceRendererTest extends TestCase
{
    /**
     * @test
     */
    public function it_renders_html_for_a_stacktrace()
    {
        $testItem = new StacktraceRenderer(new \Exception('<test>'));
        $actual = $testItem->__toString();
        $this->assertStringNotContainsString('<body>', $actual);
        $this->assertStringContainsString('&lt;test&gt;', $actual);
        $this->assertStringContainsString((string) PHP_VERSION, $actual);
    }
}
