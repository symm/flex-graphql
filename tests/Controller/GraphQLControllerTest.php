<?php

namespace Tests\App;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GraphQLControllerTest extends WebTestCase
{
    public function testEmptyQuery()
    {
        $client = static::createClient();

        $query = [];
        $variables = [];
        $payload = json_encode(['query' => $query, 'variables' => $variables]);

        $client->request(
            'GET',
            '/',
            [],
            [],
            [],
            $payload
        );

        $this->assertJsonStringEqualsJsonString(
            '{"errors":[{"message":"GraphQL Request must include at least one of those two parameters: \"query\" or \"queryId\"","category":"request"}]}',
            $client->getResponse()->getContent()
        );
    }
}