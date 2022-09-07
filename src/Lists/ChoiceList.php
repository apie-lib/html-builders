<?php
namespace Apie\HtmlBuilders\Lists;

use Apie\Core\Lists\ItemList;
use Apie\HtmlBuilders\Dto\Choice;
use ReflectionEnum;
use ReflectionEnumBackedCase;
use ReflectionEnumUnitCase;

class ChoiceList extends ItemList
{
    public function offsetGet(mixed $offset): Choice
    {
        return parent::offsetGet($offset);
    }

    public static function createFromEnum(ReflectionEnum $enum, bool $addNullOption = false): self
    {
        return new self(
            array_merge(
                $addNullOption ? [new Choice('', '-')] : [],
                array_map(
                    function (ReflectionEnumUnitCase $enum) {
                        if ($enum instanceof ReflectionEnumBackedCase) {
                            return new Choice((string) $enum->getName(), (string) $enum->getBackingValue());
                        }
                        return new Choice((string) $enum->getName(), (string) $enum->getName());
                    },
                    $enum->getCases()
                )
            )
        );
    }
}
