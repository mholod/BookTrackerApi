<?php

namespace App\Repository;

use App\Entity\Book;
use App\Resource\BookResource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\UuidInterface;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function getBookById(UuidInterface $bookId): BookResource
    {
        $qb = $this->createQueryBuilder('b');

        $qb->select(sprintf(
            'NEW %s(
                b.id,
                b.title,
                b.author,
                b.dateRead,
                b.review
            )',
            BookResource::class
        ))
            ->where('b.id = :id')
            ->setParameter('id', $bookId);

        /** @var BookResource|null $result */
        $result = $qb->getQuery()->getOneOrNullResult();

        if ($result === null) {
            throw new EntityNotFoundException('Book not found');
        }

        return $result;

        // SQL solution
//        $sql = <<<SQL
//            SELECT
//                b.*
//            FROM book b
//            WHERE b.id = :id
//        SQL;
//
//        /**
//         * @var  array{
//         *     'id': string,
//         *     'title': string,
//         *     'author': string,
//         *     'date_read': string,
//         *     'review': string,
//         * }|false
//         *
//         */
//        $result = $this->registry->getConnection()
//            ->executeQuery($sql, [
//                'id' => $bookId->toString(),
//            ])
//            ->fetchAssociative();
//
//        if ($result === false) {
//            throw new EntityNotFoundException('Book not found');
//        }
//
//        return new BookResource(
//            id: Uuid::fromString($result['id']),
//            title: $result['title'],
//            author: $result['author'],
//            dateRead: $result['date_read'],
//            review: $result['review'],
//        );
    }

    /**
     * @return BookResource[]
     */
    public function getBooksByOwner(UuidInterface $owner): array
    {
        $qb = $this->createQueryBuilder('b');

        $qb->select(sprintf(
            'NEW %s(
                b.id,
                b.title,
                b.author,
                b.dateRead,
                b.review
            )',
            BookResource::class
        ))
            ->where('b.owner = :owner')
            ->setParameter('owner', $owner)
            ->orderBy('b.dateRead', 'DESC');

        /** @var BookResource[] $results */
        $results = $qb->getQuery()->getResult();

        return $results;
    }
}
