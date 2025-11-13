<?php

declare(strict_types=1);

namespace App\Resource;

use Ramsey\Uuid\UuidInterface;

class BookResource
{
    public function __construct(
        public UuidInterface $id,
        public string $title,
        public string $author,
        public \DateTimeImmutable $dateRead,
        public ?string $review = null,
    ) {
    }
}