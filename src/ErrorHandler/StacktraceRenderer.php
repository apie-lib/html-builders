<?php
namespace Apie\HtmlBuilders\ErrorHandler;

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
        $html = str_replace(['<html ', '</html>'], ['<div ', '</div>'], $html);
        $html = str_replace('height:auto', '', $html);
        $html .= '<script>(function () {
        for (const elm of document.querySelectorAll("#app > nav")) { elm.remove(); }
        const sections = document.querySelectorAll("main section");
        // remove flare add:
        sections[sections.length - 1].remove();
        }());
        </script>';
        return $html;
    }
}