<?php declare(strict_types=1);

namespace Tests\App;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class GraphQLControllerTest extends WebTestCase
{
    public function testEmptyQuery()
    {
        $response = $this->doGraphQLQuery('');

        $this->assertJsonStringEqualsJsonString(
            '{"errors":[{"message":"GraphQL Request must include at least one of those two parameters: \"query\" or \"queryId\"","category":"request"}]}',
            $response->getContent()
        );
    }

    /**
     * @dataProvider greetingProvider
     */
    public function testEchoQuery(string $greeting)
    {
        $response = $this->doGraphQLQuery(
            '
            query ($message: String) {
              echo(message: $message)
            }
            ',
            [
                'message' => $greeting,
            ]
        );

        $this->assertJsonStringEqualsJsonString(
            '{"data":{"echo":"' . $greeting . '"}}',
            $response->getContent()
        );
    }


    public function greetingProvider(): array
    {
        return [
            ['Hello'],
            ['Bonjour'],
            ['Guten Tag'],
            ['Salut'],
        ];

    }

    private function doGraphQLQuery(?string $query = null, array $variables = []): Response
    {
        $client = static::createClient();

        $payload = [];

        if ($query) {
            $payload['query'] = $query;
        }

        if (count($variables) > 0) {
            $payload['variables'] = $variables;
        }

        $client->request(
            'GET',
            '/' . '?' . http_build_query($payload),
            [],
            [],
            [],
            []
        );

        $response = $client->getResponse();

        if (!$response) {
            throw new \RuntimeException('No response');
        }

        return $response;
    }
}