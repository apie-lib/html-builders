<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\Core\Enums\RequestMethod;
use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Lists\ComponentHashmap;
use ReflectionClass;

class PolymorphicForm extends BaseComponent
{
    /**
     * @param ReflectionClass<object> $class
     * @param array<string, string> $formValidationErrors
     */
    public function __construct(
        RequestMethod $method,
        ReflectionClass $class,
        ?string $validationError,
        array $formValidationErrors,
        mixed $value,
        bool $multipart,
        Csrf $csrf,
        ComponentHashmap $discriminatorMap
    ) {
        $csrfToken = $csrf->getAttribute('token');
        $subtypes = [];
        $mapping = [
            '_csrf' => $csrf,
        ];
        foreach ($discriminatorMap as $name => $component) {
            $mapping[$name] = $this->makePrototype(md5($class->name . $method->name . $name), $component);
            $subtypes[] = $name;
        }
        parent::__construct(
            [
                'method' => $method->value,
                'value' => $value,
                'csrf' => $csrfToken,
                'multipart' => $multipart,
                'subtypes' => $subtypes,
                'validationError' => $validationError,
                'validationErrors' => $formValidationErrors,
            ],
            new ComponentHashmap($mapping)
        );
    }
}
