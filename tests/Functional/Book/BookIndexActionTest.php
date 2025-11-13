<?php

declare(strict_types=1);

namespace App\Tests\Functional\Book;

use App\Tests\Functional\FunctionalTestCase;

class BookIndexActionTest extends FunctionalTestCase
{
    public function testIndex(): void
    {
        $this->get('/api/books');
    }
}