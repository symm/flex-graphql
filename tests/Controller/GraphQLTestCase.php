<?php declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\GraphQLTestClient;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class GraphQLTestCase extends WebTestCase
{
    /** @var GraphQLTestClient */
    protected $client;

    public function setUp(): void
    {
        $this->client = new GraphQLTestClient(static::createClient());
    }

    protected function assertDefaultHeaders(Response $response): void
    {
        $this->assertEquals('application/json', $response->headers->get('content-type'));
        $this->assertEquals('no-cache, private', $response->headers->get('cache-control'));
        $this->assertNotNull($response->headers->get('date'));
    }

    protected function assertStatusCode(int $expectedStatusCode, Response $response): void
    {
        $this->assertEquals($expectedStatusCode, $response->getStatusCode(), 'Status code did not match');
    }

    protected function decodeResponse(Response $response): array
    {
        $decoded = json_decode($response->getContent(), true);

        if ($decoded === null) {
            $message = sprintf('Error decoding JSON: %s %s', json_last_error(), json_last_error_msg());
            throw new \RuntimeException($message);
        }

        return $decoded;
    }

    protected function seeJsonStructure(array $structure, array $responseData)
    {
        foreach ($structure as $key => $value) {
            if (is_array($value) && $key === '*') {
                $this->assertInternalType('array', $responseData);
                foreach ($responseData as $responseDataItem) {
                    $this->seeJsonStructure($structure['*'], $responseDataItem);
                }
            } elseif (is_array($value)) {
                $this->assertArrayHasKey($key, $responseData);
                $this->seeJsonStructure($structure[$key], $responseData[$key]);
            } else {
                $this->assertArrayHasKey($value, $responseData);
            }
        }

        return $this;
    }

    protected function assertResponseHasErrors(Response $response)
    {
        $this->seeJsonStructure([
            'errors' => [
                '*' => [
                    'message',
                    'category',
                ]
            ]
        ], $this->decodeResponse($response));
    }

    protected function assertErrorMessagePresent(string $message, string $category, Response $response)
    {
        $decodedResponse = $this->decodeResponse($response);
        $this->assertContains(
            [
                'message' => $message,
                'category' => $category,
            ],
            $decodedResponse['errors']
        );

    }

    protected function assertContentType(string $expectedContentType, Response $response)
    {
        $this->assertEquals($expectedContentType, $response->headers->get('content-type'));
    }
}