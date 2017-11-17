<?php declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\GraphQLTestClient;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class GraphQLControllerTest extends WebTestCase
{
    /** @var GraphQLTestClient */
    private $client;

    public function setUp(): void
    {
        parent::setUp();
        $this->client = new GraphQLTestClient(static::createClient());
    }

    private function assertDefaultHeaders($response)
    {
        $this->assertEquals('application/json', $response->headers->get('content-type'));
        $this->assertEquals('no-cache, private', $response->headers->get('cache-control'));
        $this->assertNotNull($response->headers->get('date'));
    }

    public function testEmptyQueryLoadsThePlayground()
    {
        $response = $this->client->doQuery('');

        $this->assertContains('Graphcool Playground', $response->getContent());
        $this->assertEquals('text/html; charset=UTF-8', $response->headers->get('content-type'));
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
        $response = $this->client->doQuery(
            '
            query ($message: String) {
              echo(message: $message)
            }
            ',
            [
                'message' => $greeting,
            ]
        );

        $this->assertDefaultHeaders($response);
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
        $response = $this->client->doMutation(
            '
            mutation Sum($x: Int, $y: Int) {
              sum(x: $x, y: $y)
            }
            ',
            [
                'x' => $x,
                'y' => $y,
            ]
        );

        $this->assertDefaultHeaders($response);
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
}