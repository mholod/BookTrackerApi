<?php

declare(strict_types=1);

namespace App\Controller\Book;

use App\Entity\User;
use App\Response\Output;
use App\Service\Book\BookService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class BookIndexAction extends AbstractController
{
    public function __construct(
        private BookService $bookService,
    ) {
    }

    #[Route(path: '/api/books', name: 'book.index', methods: [Request::METHOD_GET])]
    public function __invoke(): Output
    {
        /** @var User $user */
        $user = $this->getUser();

        $books = $this->bookService->getBooksByOwner($user->id);

        return new Output($books);
    }
}