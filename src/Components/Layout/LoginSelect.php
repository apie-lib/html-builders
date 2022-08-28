<?php
namespace Apie\HtmlBuilders\Components\Layout;

use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Configuration\CurrentConfiguration;
use Apie\HtmlBuilders\Lists\ComponentHashmap;

class LoginSelect extends BaseComponent
{
    public function __construct(
        CurrentConfiguration $currentConfiguration
    ) {
        $loginOptions = [];
        $boundedContext = $currentConfiguration->getSelectedBoundedContext();
        if ($boundedContext) {
            foreach ($boundedContext->actions->filterOnApieContext($currentConfiguration->getApieContext()) as $method) {
                if ($method->name==='verifyAuthentication') {
                    $loginOptions[] = [
                        'name' => $method->getDeclaringClass()->getShortName(),
                        'value' => $currentConfiguration->getContextUrl(
                            '/action/' . $method->getDeclaringClass()->getShortName() . '/verifyAuthentication'
                        ),
                    ];
                }
            }
        }
        parent::__construct(
            [
                'loginOptions' => $loginOptions,
            ],
            new ComponentHashmap([
            ])
        );
    }
}
