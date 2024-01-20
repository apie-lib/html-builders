<?php
namespace Apie\HtmlBuilders\Components\Layout;

use Apie\Common\ContextConstants;
use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Components\Dashboard\RawContents;
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
                if ($method->name==='verifyAuthentication' || $method->name==='login') {
                    $loginOptions[] = [
                        'name' => $method->getDeclaringClass()->getShortName(),
                        'value' => $currentConfiguration->getContextUrl(
                            '/action/' . $method->getDeclaringClass()->getShortName() . '/' . $method->name
                        ),
                    ];
                }
            }
        }
        $profile = new RawContents('');
        if ($currentConfiguration->getApieContext()->hasContext(ContextConstants::AUTHENTICATED_USER)) {
            $profile = new ShowProfile(
                $currentConfiguration,
                $currentConfiguration->getApieContext()->getContext(ContextConstants::AUTHENTICATED_USER)
            );
        }
        parent::__construct(
            [
                'loginOptions' => $loginOptions,
            ],
            new ComponentHashmap([
                'profile' => $profile,
            ])
        );
    }
}
