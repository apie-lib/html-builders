<?php
namespace Apie\HtmlBuilders\ErrorHandler;

use DOMDocument;
use Spatie\Ignition\Ignition;
use Stringable;
use Throwable;

final class StacktraceRenderer implements Stringable
{
    public function __construct(
        private readonly Throwable $error
    ) {
    }

    public function __toString(): string
    {
        ob_start();
        Ignition::make()->renderException($this->error);
        $html = ob_get_clean();
        if (preg_match('#<body[^>]*>(?<innerHtml>.*)</body#smi', $html, $matches)) {
            return $matches['innerHtml'] ?? $html;
        }
        return $html;
    }
}