<?php

declare(strict_types=1);

namespace App\Controller\Book;

use App\DTO\Book\UpdateBookDTO;
use App\Response\Output;
use App\Service\Book\BookService;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class BookUpdateAction extends AbstractController
{
    public function __construct(private BookService $bookService)
    {
    }

    #[Route('/api/books/{bookId}/edit', name: 'book.edit', methods: [Request::METHOD_PATCH])]
    public function __invoke(UuidInterface $bookId, #[MapRequestPayload] UpdateBookDTO $input): Output
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $book = $this->bookService->updateBook($bookId, $input, $user->getId());

        return new Output($book, null, Response::HTTP_OK);
    }
}