<?php declare(strict_types=1);

namespace App\Tests\Controller;


class GraphQLControllerTest extends GraphQLTestCase
{
    public function testEmptyQueryLoadsThePlayground()
    {
        $response = $this->client->doQuery('');

        $this->assertContains('GraphQL Playground', $response->getContent());
        $this->assertEquals('text/html; charset=UTF-8', $response->headers->get('content-type'));
    }

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

        $expected = [
            'data' => [
                'authors' => [
                    [
                        'id' => '1',
                        'name' => 'Dave',
                        'articles' => [
                            [
                                'id' => '1',
                                'title' => "Dave's Article",
                                'content' => 'All about Dave',
                            ],
                            [
                                'id' => '2',
                                'title' => 'Another',
                                'content' => 'Something',
                            ],
                        ]
                    ],
                    [
                        'id' => '2',
                        'name' => 'Rob',
                        'articles' => [
                            [
                                'id' => '3',
                                'title' => "Rob's Article",
                                'content' => 'All about Rob',
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $decoded, 'Response content did not match');
    }
}