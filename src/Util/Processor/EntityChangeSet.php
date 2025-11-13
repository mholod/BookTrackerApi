<?php

declare(strict_types=1);

namespace App\Util\Processor;

class EntityChangeSet
{
    /**
     * @param array<string, FieldChangeSet> $fieldChangeSets
     */
    public function __construct(
        public private(set) array $fieldChangeSets = [],
    ) {
    }

    public function isEmpty(): bool
    {
        return count($this->fieldChangeSets) === 0;
    }

    public function add(string $field, FieldChangeSet $changeSet): static
    {
        $this->fieldChangeSets[$field] = $changeSet;

        return $this;
    }

    /**
     * @return array<string, array{old: mixed, new: mixed}>
     */
    public function toArray(): array
    {
        $array = array_map(fn (FieldChangeSet $changeSet) => [
            'old' => $this->normalizeValueToScalar($changeSet->oldValue),
            'new' => $this->normalizeValueToScalar($changeSet->newValue),
        ], $this->fieldChangeSets);

        ksort($array);

        return $array;
    }

    private function normalizeValueToScalar(mixed $value): mixed
    {
        if ($value === null) {
            return $value;
        }

        if (!is_scalar($value)) {
            if ($value instanceof \DateTimeInterface) {
                return $value->format('c');
            }

            if (is_array($value)) {
                return array_map(fn (mixed $item) => $this->normalizeValueToScalar($item), $value);
            }

            try {
                $jsonValue = \Safe\json_encode($value);

                return $jsonValue;
            } catch (\JsonException) {
                return serialize($value);
            }
        }

        return $value;
    }
}
