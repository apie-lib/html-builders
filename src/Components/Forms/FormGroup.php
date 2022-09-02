<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Lists\ComponentHashmap;

class FormGroup extends BaseComponent
{
    public function __construct(string $groupName, ComponentInterface... $formElements)
    {
        parent::__construct(
            [
                'groupName' => $groupName,
                'keys' => array_keys($formElements),
            ],
            new ComponentHashmap($formElements)
        );
    }
}
