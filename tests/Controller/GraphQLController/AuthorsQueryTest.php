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