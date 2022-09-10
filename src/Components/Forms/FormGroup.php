<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Lists\ComponentHashmap;
use Apie\HtmlBuilders\ValueObjects\FormName;

class FormGroup extends BaseComponent
{
    public function __construct(FormName $name, ComponentInterface... $formElements)
    {
        // empty form: we need to send something so we can serialize {} in the form submit
        if (empty($formElements)) {
            $formElements['_type'] = new HiddenField(
                $name->getTypehintName(),
                'object'
            );
        }
        parent::__construct(
            [
                'groupName' => $name,
                'keys' => array_keys($formElements),
            ],
            new ComponentHashmap($formElements)
        );
    }
}
