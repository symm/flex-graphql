<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Response;

class GraphQLTestClient
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function doQuery(?string $query, array $variables = []): Response
    {
        return $this->performRequest($query, $variables, 'GET');
    }

    public function doMutation(?string $query, array $variables = []): Response
    {
        return $this->performRequest($query, $variables, 'POST');
    }

    private function performRequest(?string $query, array $variables = [], string $method): Response
    {
        $query = trim($query);
        $payload = [];

        $queryString = '';
        $content = '';

        if ($query) {
            $payload['query'] = $query;
        }

        if (count($variables) > 0) {
            $payload['variables'] = $variables;
        }

        if ('GET' === $method) {
            $queryString = '?'.http_build_query($payload);
        }

        if ('POST' === $method) {
            $content = json_encode($payload);
        }

        $this->client->request(
            $method,
            '/'.$queryString,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_USER_AGENT' => 'GraphQL Test Client',
            ],
            $content
        );

        $response = $this->client->getResponse();

        if (!$response) {
            throw new \RuntimeException('No response');
        }

        return $response;
    }
}
