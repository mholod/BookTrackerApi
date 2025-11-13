<?php

declare(strict_types=1);

namespace App\Service\Book;

use App\DTO\Book\CreateBookDTO;
use App\DTO\Book\UpdateBookDTO;
use App\Entity\Book;
use App\Entity\User;
use App\Repository\BookRepository;
use App\Resource\BookResource;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class BookService
{
    public function __construct(
        private BookRepository $bookRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return BookResource[]
     */
    public function getBooksByOwner(UuidInterface $owner): array
    {
        return $this->bookRepository->getBooksByOwner($owner);
    }

    public function getBookById(UuidInterface $id): BookResource
    {
        return $this->bookRepository->getBookById($id);
    }

    public function createBook(CreateBookDTO $input, User $owner): BookResource
    {
        if (!$owner instanceof User) {
            throw new \InvalidArgumentException('Expected App\Entity\User');
        }

        $book = new Book();
        $book->title = $input->title;
        $book->author = $input->author;
        $book->review = $input->review;
        $book->dateRead = $input->getDateRead();
        $book->owner = $owner;

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return new BookResource(
            id: $book->id,
            title: $book->title,
            author: $book->author,
            dateRead: $book->dateRead,
            review: $book->review,
        );
    }

    public function deleteBook(UuidInterface $bookId, UuidInterface $ownerId): void
    {
        /** @var Book|null $book */
        $book = $this->bookRepository->find($bookId);

        if ($book === null) {
            throw new EntityNotFoundException('Book not found');
        }

        if ($book->owner->getId()->toString() !== $ownerId->toString()) {
            throw new AccessDeniedException('Not allowed to delete this book');
        }

        $this->entityManager->remove($book);
        $this->entityManager->flush();
    }

    public function updateBook(UuidInterface $bookId, UpdateBookDTO $input, UuidInterface $ownerId): BookResource
    {
        /** @var Book|null $book */
        $book = $this->bookRepository->find($bookId);

        if ($book === null) {
            throw new EntityNotFoundException('Book not found');
        }

        if ($book->owner->getId()->toString() !== $ownerId->toString()) {
            throw new AccessDeniedException('Not allowed to update this book');
        }

        if ($input->title !== null) {
            $book->title = $input->title;
        }

        if ($input->author !== null) {
            $book->author = $input->author;
        }

        if ($input->dateRead !== null) {
            $book->dateRead = $input->getDateRead();
        }

        if ($input->review !== null) {
            $book->review = $input->review;
        }

        $this->entityManager->flush();

        return new BookResource(
            id: $book->id,
            title: $book->title,
            author: $book->author,
            dateRead: $book->dateRead,
            review: $book->review,
        );
    }
}