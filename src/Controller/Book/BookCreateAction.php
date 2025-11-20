<?php

declare(strict_types=1);

namespace App\Controller\Book;

use App\DTO\Book\CreateBookDTO;
use App\Entity\User;
use App\Response\Output;
use App\Service\Book\BookService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

class BookCreateAction extends AbstractController
{
    public function __construct(private BookService $bookService)
    {
    }

    #[Route('/books', name: 'book.create', methods: [Request::METHOD_POST])]
    public function __invoke(#[MapRequestPayload] CreateBookDTO $input): Output
    {
        /** @var User $user */
        $user = $this->getUser();

        $book = $this->bookService->createBook($input, $user);

        return  new Output(
            data: $book,
            responseCode: Response::HTTP_CREATED,
        );
    }
}