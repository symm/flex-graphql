<?php declare(strict_types=1);

namespace App\Tests\Controller;

class MaxDepthQueryTest extends GraphQLTestCase
{

    public function testErrorsWhenTheQueryGoesTooDeep()
    {
        $response = $this->client->doQuery('
        {
          articles {
            author {
              articles {
                author {
                  articles {
                    author {
                      articles {
                        author {
                          articles {
                            author {
                              articles {
                                author {
                                  articles {
                                    id
                                  }
                                }
                              }
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
        ');

        $this->assertStatusCode(400, $response);
        $this->assertResponseHasErrors($response);
        $this->assertErrorMessagePresent('Max query depth should be 11 but got 12.', 'graphql', $response);
    }
}