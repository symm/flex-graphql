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

        $expected = [
            'data' => [
                'articles' => [
                    [
                        'id' => '1',
                        'author' => [
                            'id' => '1',
                            'name' => 'Dave',
                        ],
                        'title' => "Dave's Article",
                        'content' => 'All about Dave',
                    ],
                    [
                        'id' => '2',
                        'author' => [
                            'id' => '1',
                            'name' => 'Dave',
                        ],
                        'title' => 'Another',
                        'content' => 'Something',
                    ],
                    [
                        'id' => '3',
                        'author' => [
                            'id' => '2',
                            'name' => 'Rob',
                        ],
                        'title' => "Rob's Article",
                        'content' => 'All about Rob',
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $decoded, 'Response content did not match');
    }
}