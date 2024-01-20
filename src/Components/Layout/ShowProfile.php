<?php
namespace Apie\HtmlBuilders\Components\Layout;

use Apie\CommonValueObjects\Email;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Configuration\CurrentConfiguration;
use Apie\HtmlBuilders\Lists\ComponentHashmap;
use ReflectionClass;
use Symfony\Component\PropertyAccess\PropertyAccessorBuilder;

class ShowProfile extends BaseComponent
{
    public function __construct(
        CurrentConfiguration $currentConfiguration,
        EntityInterface $user
    ) {
        $propertyAccess = (new PropertyAccessorBuilder())->getPropertyAccessor();
        $email = null;
        if ($propertyAccess->isReadable($user, 'email')) {
            $email = $propertyAccess->getValue($user, 'email');
        } else if ($propertyAccess->isReadable($user, 'id')) {
            try {
                $email = (new Email($propertyAccess->getValue($user, 'id')))->toNative();
            } catch (InvalidStringForValueObjectException) {
            }
        }
        $baseClass = $user->getId()::getReferenceFor();
        $boundedContextId = $currentConfiguration->getSelectedBoundedContextId();
        $boundedContextId = $currentConfiguration->getBoundedContextHashmap()->getBoundedContextFromClassName(
            new ReflectionClass($user),
            $boundedContextId
        )?->getId() ?? $boundedContextId;
        
        $profileUrl = $currentConfiguration->getGlobalUrl(
            $boundedContextId . '/resource/' . $baseClass->getShortName() . '/' . $user->getId()->toNative()
        );
        parent::__construct(
            [
                'user' => $user,
                'email' => $email,
                'profileUrl' => $profileUrl,
                'gravatarUrl' => 'https://gravatar.com/avatar/' . md5(strtolower($email ?? '')),
            ],
            new ComponentHashmap([
            ])
        );
    }
}
