<?php

declare(strict_types=1);

namespace App\Resolver;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class RamseyUuidValueResolver implements ValueResolverInterface
{
    /**
     * @return iterable<int, UuidInterface>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== UuidInterface::class) {
            return [];
        }

        $value = $request->attributes->get($argument->getName());
        if ($value === null) {
            return [];
        }

        yield Uuid::fromString($value);
    }
}