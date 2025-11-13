<?php

namespace App\Encoder;

/**
 * All classes that implement this interface will be tagged with `app.encoder.schema`.
 *
 * It's used to automatically build the mapping for [resource => schema] in the Neomerx/Json-api library
 */
interface EncodesResourceInterface
{
    /**
     * This should return the FQCN of the resource that the schema encodes.
     *
     * e.g. BookSchema is responsible for encoding the Book entity - it's return value would be 'Book::class'
     */
    public function encodesResource(): string;
}
