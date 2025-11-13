<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Book;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Service\Book\BookService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class BookServiceTest extends TestCase
{
    private BookRepository $bookRepository;
    private BookService $bookService;

    protected function setUp(): void
    {
        $this->bookRepository = $this->createMock(BookRepository::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $this->bookService = new BookService($this->bookRepository, $entityManager);
    }

    public function testGetBooksByOwnerReturnsBooks(): void
    {
        $owner = Uuid::uuid4();
        $book1 = $this->createMock(Book::class);
        $book2 = $this->createMock(Book::class);

        $this->bookRepository
            ->expects(self::once())
            ->method('getBooksByOwner')
            ->with($owner)
            ->willReturn([$book1, $book2]);

        $result = $this->bookService->getBooksByOwner($owner);

        self::assertCount(2, $result);
        self::assertContainsOnlyInstancesOf(Book::class, $result);
        self::assertSame([$book1, $book2], $result);
    }

    public function testGetBooksByOwnerReturnsEmptyArrayWhenNoBooks(): void
    {
        $owner = Uuid::uuid4();

        $this->bookRepository
            ->expects(self::once())
            ->method('getBooksByOwner')
            ->with($owner)
            ->willReturn([]);

        $result = $this->bookService->getBooksByOwner($owner);

        self::assertIsArray($result);
        self::assertEmpty($result);
    }
}