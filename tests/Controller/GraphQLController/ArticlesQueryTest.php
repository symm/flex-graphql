<?php declare(strict_types=1);

namespace App\Tests\Controller\GraphQLController;

use App\Tests\Controller\GraphQLTestCase;

class ArticlesQueryTest extends GraphQLTestCase
{
    public function testArticlesQuery()
    {
        $response = $this->client->doQuery(
            '
           {
              articles {
                id
                author {
                  id
                  name
                }
                title
                content
              }
            }
           '
        );

        $this->assertDefaultHeaders($response);
        $this->assertStatusCode(200, $response);

        $decoded = $this->decodeResponse($response);

        $this->seeJsonStructure([
            'data' => [
                'articles' => ['*' => [
                    'id',
                    'author' => [
                        'id',
                        'name',
                    ],
                    'title',
                    'content',
                ]]
            ]
        ],
            $decoded
        );

        $this->assertCount(100, $decoded['data']['articles']);
    }
}