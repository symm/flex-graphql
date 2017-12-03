<?php

declare(strict_types=1);

namespace App\Tests\Controller\GraphQLController;

use App\Tests\Controller\GraphQLTestCase;

class EmptyQueryTest extends GraphQLTestCase
{
    public function testEmptyQueryLoadsThePlayground()
    {
        $response = $this->client->doQuery('');

        $this->assertContains('GraphQL Playground', $response->getContent());
        $this->assertContentType('text/html; charset=UTF-8', $response);
    }
}
