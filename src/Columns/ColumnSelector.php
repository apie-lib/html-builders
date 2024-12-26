<?php
namespace Apie\HtmlBuilders\Columns;

use Apie\Core\Attributes\HideIdOnOverview;
use Apie\Core\Context\ApieContext;
use Apie\Core\Entities\PolymorphicEntityInterface;
use Apie\Core\Other\DiscriminatorMapping;
use Generator;
use ReflectionClass;

final class ColumnSelector
{
    /**
     * @param ReflectionClass<object> $class
     * @return array<int, string>
     */
    public function getColumns(ReflectionClass $class, ApieContext $context): array
    {
        $done = [];
        $columns = $this->getInternalColumns($class, $context, $done);
        if ($class->getAttributes(HideIdOnOverview::class)) {
            $columns = array_values(array_filter($columns, function ($value) { return $value !== 'id'; }));
        }
        return $columns;
    }

    /**
     * @param ReflectionClass<object> $class
     * @param array<string, bool> $internalDone
     * @return array<int, string>
     */
    public function getInternalColumns(ReflectionClass $class, ApieContext $context, array& $internalDone): array
    {
        if (isset($internalDone[$class->name])) {
            return [];
        }
        $internalDone[$class->name] = true;
        $columns = $this->getFromSingleClass($class, $context);
        usort($columns, [$this, 'sortCallback']);
        if ($class->implementsInterface(PolymorphicEntityInterface::class)) {
            $done = [];
            $discriminatorColumns = $this->getPolymorphicColumns($class, $done);
            $columns = [...array_slice($columns, 0, 1), ...$discriminatorColumns, ...array_slice($columns, 1)];
            $done = [];
            foreach ($this->iterateOverChildClasses($class, $done) as $childClass) {
                $columns = [...$columns, ...$this->getInternalColumns($childClass, $context, $internalDone)];
            }
            $columns = array_values(array_unique($columns));
        }

        return $columns;
    }

    /**
     * @param ReflectionClass<object> $class
     * @return Generator<int, DiscriminatorMapping>
     */
    private function iterateOverDiscriminatorMappings(ReflectionClass $class): Generator
    {
        while ($class) {
            $method = $class->getMethod('getDiscriminatorMapping');
            if ($method->getDeclaringClass()->name === $class->name && !$method->isAbstract()) {
                yield $method->invoke(null);
            }
            $class = $class->getParentClass();
        }
    }

    /**
     * @param ReflectionClass<object> $class
     * @param array<int|string, string> $done
     * @param-out array<int|string, string> $done
     * @return Generator<int, ReflectionClass<PolymorphicEntityInterface>>
     */
    private function iterateOverChildClasses(ReflectionClass $class, array& $done): Generator
    {
        $method = $class->getMethod('getDiscriminatorMapping');
        $declaredClass = $method->getDeclaringClass()->name;
        if (in_array($declaredClass, $done) || $class->name !== $declaredClass) {
            return;
        }
        $done[] = $declaredClass;
        $mapping = $method->invoke(null);
        foreach ($mapping->getConfigs() as $config) {
            $refl = new ReflectionClass($config->getClassName());
            yield $refl;
            yield from $this->iterateOverChildClasses($refl, $done);
        }
    }

    /**
     * @param ReflectionClass<object> $class
     * @param array<string, bool> $done
     * @return array<int, string>
     */
    public function getPolymorphicColumns(ReflectionClass $class, array& $done): array
    {
        if (isset($done[$class->name])) {
            return [];
        }
        $done[$class->name] = true;
        $list = [];
        foreach ($this->iterateOverDiscriminatorMappings($class) as $mapping) {
            $list[] = $mapping->getPropertyName();
            foreach ($mapping->getConfigs() as $config) {
                array_push($list, ...$this->getPolymorphicColumns(new ReflectionClass($config->getClassName()), $done));
            }
        }
        
        return $list;
    }

    /**
     * @param ReflectionClass<object> $class
     * @return array<int, string>
     */
    private function getFromSingleClass(ReflectionClass $class, ApieContext $context): array
    {
        $columns = array_keys($context->getApplicableGetters($class)->toArray());

        return $columns;
    }

    private function sortCallback(string $input1, string $input2): int
    {
        $rating1 = $this->rating($input1);
        $rating2 = $this->rating($input2);

        if ($rating1 === $rating2) {
            return $input1 <=> $input2;
        }
        return $rating1 <=> $rating2;
    }

    private function rating(string $input): int
    {
        if (stripos($input, 'status') !== false) {
            return -150;
        }
        $ratings = [
            'id' => -300,
            'name' => -250,
            'email' => -200,
            'description' => 100,
        ];
        return $ratings[$input] ?? 0;
    }
}
