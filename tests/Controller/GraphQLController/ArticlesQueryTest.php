<?php

declare(strict_types=1);

namespace App\Tests\Controller\GraphQLController;

use App\Entity\Article;
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

        $this->seeJsonStructure(
            [
            'data' => [
                'articles' => ['*' => [
                    'id',
                    'author' => [
                        'id',
                        'name',
                    ],
                    'title',
                    'content',
                ]],
            ],
        ],
            $decoded
        );

        foreach ($decoded['data']['articles'] as $article) {
            $fixture = $this->findFixtureById(Article::class, $article['id']);

            $this->assertEquals($fixture->getId(), $article['id'], 'Article ID did not match');
            $this->assertEquals($fixture->getTitle(), $article['title'], 'Article title did not match');
            $this->assertEquals($fixture->getContent(), $article['content'], 'Article Content did not match');
            $this->assertEquals($fixture->getAuthor()->getId()->toString(), $article['author']['id'], 'Article Author ID did not match');
            $this->assertEquals($fixture->getAuthor()->getName(), $article['author']['name'], 'Article Author Name did not match');
        }
    }
}
