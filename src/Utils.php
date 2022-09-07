<?php
namespace Apie\HtmlBuilders;

use UnexpectedValueException;

final class Utils
{
    private function __construct()
    {
    }

    public static function toFormName(array $prefixes): string
    {
        if (empty($prefixes)) {
            return '';
        }
        $name = array_shift($prefixes);
        while (!empty($prefixes)) {
            $name .= '[' . array_shift($prefixes) . ']';
        }
        return $name;
    }

    public static function internalName(string $name): string
    {
        if (!preg_match('/^(?<first>[^\[]+)(?<second>.*)$/', $name, $matches)) {
            throw new UnexpectedValueException('Can not parse ' . $name);
        }
        return '_apie[' . $matches['first'] . ']' . $matches['second'];
    }
}
