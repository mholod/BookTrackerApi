<?php

declare(strict_types=1);

namespace App\Util\Processor;

readonly class FieldChangeSet
{
    public function __construct(
        public mixed $oldValue,
        public mixed $newValue,
    ) {
    }
}
