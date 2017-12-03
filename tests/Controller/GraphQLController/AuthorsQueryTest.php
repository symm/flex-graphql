<?php

declare(strict_types=1);

namespace App\Tests\Controller\GraphQLController;

use App\Entity\Author;
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

        $this->seeJsonStructure(
            [
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
                            ],
                        ],
                    ],
                ],
            ],
        ],
            $decoded
        );

        foreach ($decoded['data']['authors'] as $author) {
            /** @var Author $fixture */
            $fixture = $this->findFixtureById(Author::class, $author['id']);

            $this->assertEquals($fixture->getId()->toString(), $author['id'], 'Author ID did not match');
            $this->assertEquals($fixture->getName(), $author['name'], 'Author name did not match');
            $this->assertCount($fixture->getArticles()->count(), $author['articles'], 'Article count did not match');
        }
    }
}
