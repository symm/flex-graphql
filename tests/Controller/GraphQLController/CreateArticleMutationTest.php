<?php

declare(strict_types=1);

namespace App\Tests\Controller\GraphQLController;

use App\Tests\Controller\GraphQLTestCase;

class CreateArticleMutationTest extends GraphQLTestCase
{
    public function testItCreatesANewArticle()
    {
        $response = $this->client->doMutation('
        mutation CreateArticleMutation($input: CreateArticleInput!) {
          createArticle(input: $input) {
            article {
              id
              title
              content
            }
          }
        }
        ', [
            'input' => [
                'title' => 'My Article',
                'content' => 'The content of the article',
            ],
        ]);

        $decodedResponse = $this->decodeResponse($response);

        $this->assertNoErrors($decodedResponse);

        $this->seeJsonStructure([
            'data' => [
                'createArticle',
            ],
        ], $decodedResponse);

        $this->assertNotNull($decodedResponse['data']['createArticle']);
        $this->assertEquals('My Article', $decodedResponse['data']['createArticle']['article']['title']);
        $this->assertEquals('The content of the article', $decodedResponse['data']['createArticle']['article']['content']);
    }

    private function assertNoErrors(array $decodedResponse)
    {
        $this->assertArrayNotHasKey('errors', $decodedResponse, json_encode($decodedResponse, JSON_PRETTY_PRINT));
    }
}
