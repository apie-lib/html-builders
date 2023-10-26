<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\Core\Entities\EntityInterface;
use Apie\HtmlBuilders\Components\BaseComponent;
use ReflectionClass;

class RemoveConfirm extends BaseComponent
{
    /**
     * @param ReflectionClass<EntityInterface> $class
     */
    public function __construct(ReflectionClass $class)
    {
        parent::__construct(['class' => $class]);
    }
}
