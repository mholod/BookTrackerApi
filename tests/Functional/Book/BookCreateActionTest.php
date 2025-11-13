<?php

declare(strict_types=1);

namespace App\Tests\Functional\Book;

use App\Tests\Functional\FunctionalTestCase;

class BookCreateActionTest extends FunctionalTestCase
{
    public function testCreateBook(): void
    {
        $payload = [
            'title'  => 'Books title example',
            'author' => 'Author name example',
            'date_read' => '2024-10-01',
            'review' => 'Review example',
        ];


        $response = $this->post(
            uri: '/api/books',
            data: $payload,
        );

        $this->assertEquals($payload, $response['data']['attributes']);
    }
}