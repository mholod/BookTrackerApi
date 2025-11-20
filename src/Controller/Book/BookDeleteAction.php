<?php

declare(strict_types=1);

namespace App\Controller\Book;

use App\Entity\User;
use App\Response\Output;
use App\Service\Book\BookService;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BookDeleteAction extends AbstractController
{
    public function __construct(private BookService $bookService)
    {
    }

    #[Route('/books/{bookId}/delete', name: 'book.delete', methods: [Request::METHOD_DELETE])]
    public function __invoke(UuidInterface $bookId): Output
    {
        /** @var User $user */
        $user = $this->getUser();

        $this->bookService->deleteBook($bookId, $user->id);

        return new Output(null, null, Response::HTTP_NO_CONTENT);
    }
}