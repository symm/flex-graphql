<?php declare(strict_types=1);

namespace App\Tests\Controller\GraphQLController;

use App\Tests\Controller\GraphQLTestCase;

class AuthorsQueryTest extends GraphQLTestCase
{

    public function testAuthorsQuery()
    {
        $response = $this->client->doQuery('
        {
          authors {
            id
            name
            articles {
              id
              title
              content
            }
          }
        }
        ');

        $this->assertDefaultHeaders($response);
        $this->assertStatusCode(200, $response);

        $decoded = $this->decodeResponse($response);

        $this->seeJsonStructure([
            'data' => [
                'authors' => [
                    '*' => [
                        'id',
                        'name',
                        'articles' => [
                            '*' => [
                                'id',
                                'title',
                                'content',
                            ]
                        ]
                    ]
                ]
            ]
        ],
            $decoded
        );

        $this->assertCount(100, $decoded['data']['authors']);
    }
}