<?php
namespace Apie\HtmlBuilders\Components\Layout;

use Apie\Core\Context\ApieContext;
use Apie\Core\ContextConstants;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\ValueObjects\Utils;
use Apie\HtmlBuilders\Components\BaseComponent;
use Apie\HtmlBuilders\Configuration\CurrentConfiguration;
use Apie\Serializer\Serializer;
use DateTimeInterface;
use ReflectionClass;
use Stringable;
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
            $allFields = Utils::toArray(
                $serializer->normalize($user, $apieContext->withContext(ContextConstants::SHOW_PROFILE, true))
            );
            foreach (self::PROFILE_FIELDS as $profileFieldList) {
                foreach ($profileFieldList as $profileField) {
                    if (isset($allFields[$profileField])) {
                        if ($this->canBeDisplayed($allFields[$profileField])) {
                            $fields[$profileField] = Utils::toString($allFields[$profileField]);
                        }
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
                    $fieldValue = $propertyAccess->getValue($user, $profileField);
                    if ($this->canBeDisplayed($fieldValue)) {
                        $fields[$profileField] = Utils::toString($fieldValue);
                    }
                    break;
                }
            }
        }
        return $fields;
    }

    private function canBeDisplayed(mixed $value): bool
    {
        if (!is_object($value)) {
            return true;
        }
        if ($value instanceof Stringable || $value instanceof DateTimeInterface) {
            return true;
        }
        return ((new ReflectionClass($value))->isEnum());
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
            ]
        );
    }
}
