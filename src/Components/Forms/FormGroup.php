<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Lists\ComponentHashmap;
use Apie\HtmlBuilders\Utils;

class FormGroup extends BaseComponent
{
    public function __construct(string $groupName, ComponentInterface... $formElements)
    {
        // empty form: we need to send something so we can serialize {} in the form submit
        if (empty($formElements)) {
            $formElements['_type'] = new HiddenField(
                Utils::internalName($groupName),
                'object'
            );
        }
        parent::__construct(
            [
                'groupName' => $groupName,
                'keys' => array_keys($formElements),
            ],
            new ComponentHashmap($formElements)
        );
    }
}
