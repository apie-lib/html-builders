<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\Core\Enums\RequestMethod;
use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Lists\ComponentHashmap;
use Apie\HtmlBuilders\ValueObjects\FormName;

class Form extends BaseComponent
{
    public function __construct(RequestMethod $method, ?string $validationError, ComponentInterface... $formElements)
    {
        parent::__construct(
            [
                'method' => $method->value,
            ],
            new ComponentHashmap([
                'formElements' => new FormGroup(new FormName(), $validationError, ...$formElements),
            ])
        );
    }
}
