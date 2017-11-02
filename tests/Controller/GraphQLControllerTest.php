<?php declare(strict_types=1);

namespace Tests\App;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class GraphQLControllerTest extends WebTestCase
{
    public function testEmptyQuery()
    {
        $response = $this->performRequest('');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            '{"errors":[{"message":"GraphQL Request must include at least one of those two parameters: \"query\" or \"queryId\"","category":"request"}]}',
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

    /**
     * @dataProvider greetingProvider
     */
    public function testEchoQuery(string $greeting)
    {
        $response = $this->doQuery(
            '
            query ($message: String) {
              echo(message: $message)
            }
            ',
            [
                'message' => $greeting,
            ]
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            '{"data":{"echo":"' . $greeting . '"}}',
            $response->getContent()
        );
    }

    /**
     * @dataProvider sumProvider
     */
    public function testSumMutation(int $x, int $y, int $expected)
    {
        $response = $this->doMutation(
            '
            mutation ($x: Int, $y: Int) {
              sum(x: $x, y: $y)
            }
            ',
            [
                'x' => $x,
                'y' => $y,
            ]
        );

        $this->assertJsonStringEqualsJsonString(
            '{"data":{"sum": '. $expected .' }}',
            $response->getContent()
        );
    }


    public function sumProvider(): array
    {
        return [
            [1, 1, 2]
        ];
    }

    private function doQuery(?string $query, array $variables = []): Response
    {
        return $this->performRequest($query, $variables, 'GET');
    }

    private function doMutation(?string $query, array $variables = []): Response
    {
        return $this->performRequest($query, $variables, 'POST');
    }

    private function performRequest(?string $query, array $variables = [], $method = 'GET'): Response
    {
        $client = static::createClient();

        $payload = [];

        $queryString = '';
        $content = '';

        if ($query) {
            $payload['query'] = $query;
        }

        if (count($variables) > 0) {
            $payload['variables'] = $variables;
        }

        if ($method === 'GET') {
            $queryString = '?' . http_build_query($payload);
        }

        if ($method === 'POST') {
            $content = json_encode($payload);
        }

        $client->request(
            $method,
            '/' . $queryString,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/graphql',
                'HTTP_USER_AGENT' => 'GraphQL Test Client'
            ],
            $content
        );

        $response = $client->getResponse();

        if (!$response) {
            throw new \RuntimeException('No response');
        }

        return $response;
    }
}