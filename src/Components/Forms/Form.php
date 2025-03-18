<?php
namespace Apie\HtmlBuilders\Components\Forms;

use Apie\Core\Enums\RequestMethod;
use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\FormBuildContext;
use Apie\HtmlBuilders\Interfaces\ComponentInterface;
use Apie\HtmlBuilders\Lists\ComponentHashmap;
use Apie\HtmlBuilders\ValueObjects\FormName;

class Form extends BaseComponent
{
    /**
     * @param array<string, string> $formValidationErrors
     */
    public function __construct(
        RequestMethod $method,
        ?string $validationError,
        array $formValidationErrors,
        mixed $value,
        bool $multipart,
        ComponentInterface... $formElements
    ) {
        $csrfToken = null;
        foreach ($formElements as $formElement) {
            if ($formElement instanceof Csrf) {
                $csrfToken = $formElement->getAttribute('token');
            }
        }
        $formGroup = new FormGroup(
            new FormName(),
            $validationError,
            $formValidationErrors,
            ...$formElements
        );
        $formGroup->attributes['extraClass'] = 'unhandled-form-group';
        parent::__construct(
            [
                'method' => $method->value,
                'value' => $value,
                'csrf' => $csrfToken,
                'multipart' => $multipart,
                'validationError' => $validationError,
                'validationErrors' => $formValidationErrors,
            ],
            new ComponentHashmap([
                'formElements' => $formGroup,
            ])
        );
    }

    public function getMissingValidationErrors(FormBuildContext $formBuildContext): array
    {
        return $this->getComponent('formElements')->getMissingValidationErrors($formBuildContext);
    }
}
