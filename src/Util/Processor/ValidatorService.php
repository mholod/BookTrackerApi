<?php

declare(strict_types=1);

namespace App\Util\Processor;

use App\Exception\ValidationException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Mapping\MetadataInterface;
use Symfony\Component\Validator\Validator\ContextualValidatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * A wrapper around the Symfony Validator that adds functionality to validate and throw exception in single method
 * so that we don't have to duplicate code.
 */
class ValidatorService implements ValidatorInterface
{
    public function __construct(private readonly ValidatorInterface $validator)
    {
    }

    /**
     * @param mixed                                                 $value       The value to validate
     * @param Constraint|Constraint[]                               $constraints The constraint(s) to validate against
     * @param string|GroupSequence|array<string|GroupSequence>|null $groups      The validation groups to validate. If none is given, "Default" is assumed
     *
     * @throws \Exception
     */
    public function validateAndThrow(
        mixed $value,
        Constraint|array|null $constraints = null,
        string|GroupSequence|array|null $groups = null
    ): void {
        $violations = $this->validate($value, $constraints, $groups);

        if (count($violations) > 0) {
            throw new \Exception();
//            throw (new ValidationException())->setErrors($violations);
        }
    }

    public function __call(string $name, array $arguments): mixed
    {
        return $this->validator->{$name}(...$arguments);
    }

    public function getMetadataFor(mixed $value): MetadataInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function hasMetadataFor(mixed $value): bool
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function validate(mixed $value, $constraints = null, $groups = null): ConstraintViolationListInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function validateProperty(object $object, string $propertyName, $groups = null): ConstraintViolationListInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function validatePropertyValue($objectOrClass, string $propertyName, $value, $groups = null): ConstraintViolationListInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function startContext(): ContextualValidatorInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function inContext(ExecutionContextInterface $context): ContextualValidatorInterface
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }
}
