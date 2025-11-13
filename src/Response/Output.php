<?php

namespace App\Response;

class Output
{
    public function __construct(private readonly mixed $data, private readonly mixed $meta = null, private readonly int $responseCode = 200)
    {
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function getMeta(): mixed
    {
        return $this->meta;
    }

    public function getResponseCode(): int
    {
        return $this->responseCode;
    }
}
