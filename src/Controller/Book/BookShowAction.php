<?php

declare(strict_types=1);

namespace App\Controller\Book;

use App\Response\Output;
use App\Service\Book\BookService;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class BookShowAction
{
    public function __construct(
        private BookService $bookService,
    ) {
    }

    #[Route('/api/books/{bookId}', name: 'book.show', methods: [Request::METHOD_GET])]
    public function __invoke(UuidInterface $bookId): Output
    {
        $book = $this->bookService->getBookById($bookId);

        return new Output($book);
    }
}