<?php
namespace Apie\HtmlBuilders\Components\Layout;

use Apie\CommonValueObjects\Email;
use Apie\Core\Context\ApieContext;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use Apie\Core\ValueObjects\Utils;
use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Configuration\CurrentConfiguration;
use Apie\HtmlBuilders\Lists\ComponentHashmap;
use Apie\Serializer\Serializer;
use ReflectionClass;
use Symfony\Component\PropertyAccess\PropertyAccessorBuilder;

class ShowProfile extends BaseComponent
{
    private const PROFILE_FIELDS = [
        ['email', 'username', 'id'],
        ['fullName', 'name', 'firstName', 'lastName'],
    ];

    /**
     * @return array<string, string>
     */
    private function buildFields(EntityInterface $user, ApieContext $apieContext): array
    {
        $fields = [];
        if ($apieContext->hasContext(Serializer::class)) {
            $serializer = $apieContext->getContext(Serializer::class);
            assert($serializer instanceof Serializer);
            $allFields = Utils::toArray($serializer->normalize($user, $apieContext));
            foreach (self::PROFILE_FIELDS as $profileFieldList) {
                foreach ($profileFieldList as $profileField) {
                    if (isset($allFields[$profileField])) {
                        $fields[$profileField] = $allFields[$profileField];
                        break;
                    }
                }
            }
            return $fields;
        }
        $propertyAccess = (new PropertyAccessorBuilder())->getPropertyAccessor();
        foreach (self::PROFILE_FIELDS as $profileFieldList) {
            foreach ($profileFieldList as $profileField) {
                if ($propertyAccess->isReadable($user, $profileField)) {
                    $fields[$profileField] = $propertyAccess->getValue($user, $profileField);
                    break;
                }
            }
        }
        return $fields;
    }

    public function __construct(
        CurrentConfiguration $currentConfiguration,
        EntityInterface $user
    ) {
        $fields = $this->buildFields($user, $currentConfiguration->getApieContext());
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
                'email' => $fields['email'] ?? null,
                'profileUrl' => $profileUrl,
                'gravatarUrl' => 'https://gravatar.com/avatar/' . md5(strtolower($fields['email'] ?? '')),
                'fieldNames' => array_keys($fields),
                'fields' => $fields,
            ],
            new ComponentHashmap([
            ])
        );
    }
}
