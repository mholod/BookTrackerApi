<?php

declare(strict_types=1);

namespace App\Schema;

use App\Encoder\JsonApiSchema;
use App\Resource\BookResource;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;

class BookSchema extends JsonApiSchema
{
    public function getType(): string
    {
        return 'Book';
    }

    /**
     * @param BookResource $resource
     */
    public function getId($resource): string
    {
        return $resource->id->toString();
    }

    /**
     * @param BookResource $resource
     *
     * @return array{
     *    title: string,
     *    author: string,
     *    date_read: string,
     *    review: string|null,
     *  }
     */
    public function getAttributes($resource, ContextInterface $context): iterable
    {
        return [
            'title' => $resource->title,
            'author' => $resource->author,
            'date_read' => $resource->dateRead->format('Y-m-d'),
            'review' => $resource->review,
        ];
    }

    /**
     * @param BookResource $resource
     *
     * @return array{}
     */
    public function getRelationships($resource, ContextInterface $context): iterable
    {
        return [];
    }

    /**
     * @param BookResource $resource
     *
     * @return array{}
     */
    #[\Override]
    public function getLinks($resource): iterable
    {
        return [];
    }

    public function encodesResource(): string
    {
        return BookResource::class;
    }
}
