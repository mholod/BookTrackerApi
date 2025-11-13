<?php

declare(strict_types=1);

namespace App\Util\Processor;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Proxy;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class SimpleProcessor
{
    /**
     * @param list<class-string> $entityClasses
     */
    public function __construct(
        private readonly ValidatorService $validatorService,
        private readonly EntityManagerInterface $entityManager,
        #[Autowire(param: 'app.entities')]
        private array $entityClasses,
    ) {
    }

    private function functionCanBeInstantiatedWithoutArgs(\ReflectionFunctionAbstract $fn): bool
    {
        return (bool) ($fn->getNumberOfRequiredParameters() === 0);
    }

    /**
     * @template Target of object
     *
     * @param object|array<string,mixed>  $values
     * @param Target|class-string<Target> $target
     *
     * @return Target
     */
    public function process(object|array $values, object|string $target): object
    {
        if (is_array($values)) {
            $values = (object) $values;
        }

        $reflection = new \ReflectionObject($values);

        if (is_string($target)) {
            $targetReflectionClass = new \ReflectionClass($target);

            if (!$targetReflectionClass->isInstantiable()) {
                throw new \InvalidArgumentException(sprintf('$target must be instantiable, got %s', $target));
            }

            $targetConstructor = $targetReflectionClass->getConstructor();

            if ($targetConstructor === null || $this->functionCanBeInstantiatedWithoutArgs($targetConstructor)) {
                $target = $targetReflectionClass->newInstance();
            } else {
                $target = $targetReflectionClass->newInstanceWithoutConstructor();
            }
        }

        foreach ($reflection->getProperties() as $reflectionProperty) {
            if (!$reflectionProperty->isInitialized($values)) {
                continue;
            }

            $propertyName = $reflectionProperty->getName();

            $setter = 'set'.ucfirst($propertyName);
            if (method_exists($target, $setter)) {
                $target->$setter($reflectionProperty->getValue($values));
            } else {
                $targetReflectionClass = new \ReflectionObject($target);
                if (!$targetReflectionClass->hasProperty($propertyName)) {
                    continue;
                }

                $targetReflectionProperty = $targetReflectionClass->getProperty($propertyName);

                if ($targetReflectionProperty->isPublic() && !$targetReflectionProperty->isStatic()) {
                    $target->$propertyName = $reflectionProperty->getValue($values);
                }
            }
        }

        return $target;
    }

    /**
     * @template Target of object
     *
     * @param Target|class-string<Target> $target
     *
     * @throws \InvalidArgumentException
     */
    private function ensureTargetIsEntity(object|string $target): void
    {
        $entityClass = match (true) {
            is_string($target) => $target,
            // If the target is a proxy, then entity class should be the parent class
            $target instanceof Proxy => get_parent_class($target),
            default => $target::class,
        };

        if (!in_array($entityClass, $this->entityClasses, true)) {
            throw new \InvalidArgumentException(sprintf('$target must be an entity class name or entity instance, got %s', $entityClass));
        }
    }

    /**
     * @template Target of object
     *
     * @param object|array<string,mixed>  $values
     * @param Target|class-string<Target> $target The entity (or entity class name) to be populated
     * @param bool                        $store  If true, the entity will be validated and stored
     *
     * @return array{0: Target, 1: EntityChangeSet}
     *
     * @throws \Exception
     */
    public function processEntity(object|array $values, object|string $target, bool $store = true): array
    {
        $this->ensureTargetIsEntity($target);

        $target = $this->process($values, $target);

        if ($store) {
            $this->validatorService->validateAndThrow($target);
            $this->entityManager->persist($target);
            $entityChangeSet = $this->getEntityChangeSet($target);
            $this->entityManager->flush();
            $this->entityManager->refresh($target);
        }

        return [$target, $entityChangeSet ?? new EntityChangeSet()];
    }

    private function getEntityChangeSet(object $target): EntityChangeSet
    {
        $uow = $this->entityManager->getUnitOfWork();
        $uow->computeChangeSets();
        $changeSet = $uow->getEntityChangeSet($target);

        $ignoredFields = [
            'createdAt',
            'updatedAt',
            'createdBy',
            'updatedBy',
            'deletedAt',
            'password',
            'uuid',
        ];

        $entityChangeSet = new EntityChangeSet();
        foreach ($changeSet as $field => $change) {
            if (in_array($field, $ignoredFields, true)) {
                continue;
            }

            if ($change[0] === $change[1]) {
                continue;
            }

            $entityChangeSet->add(
                $field,
                new FieldChangeSet(
                    oldValue: $change[0],
                    newValue: $change[1],
                )
            );
        }

        return $entityChangeSet;
    }

    /**
     * Executes a function in a transaction.
     *
     * The function gets passed this EntityManager instance as an (optional) parameter.
     *
     * {@link flush} is invoked prior to transaction commit.
     *
     * If an exception occurs during execution of the function or flushing or transaction commit,
     * the transaction is rolled back, the EntityManager closed and the exception re-thrown.
     *
     * @phpstan-param callable(EntityManagerInterface): T $func The function to execute transactionally.
     *
     * @return mixed the value returned from the closure
     *
     * @phpstan-return T
     *
     * @template T
     */
    public function wrapInTransaction(callable $func): mixed
    {
        return $this->entityManager->wrapInTransaction($func);
    }
}
