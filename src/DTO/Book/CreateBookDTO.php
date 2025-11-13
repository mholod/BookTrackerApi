<?php

declare(strict_types=1);

namespace App\DTO\Book;

use App\DTO\InputInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateBookDTO implements InputInterface
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min:5, minMessage: 'Title must be at least 5 characters long')]
        public string $title,

        #[Assert\NotBlank]
        public string $author,

        #[Assert\NotBlank]
        #[Assert\Date]
        public string $dateRead,

        public ?string $review = null
    ) {}

    public function getDateRead(): \DateTimeImmutable
    {
        return new \DateTimeImmutable($this->dateRead);
    }
}
