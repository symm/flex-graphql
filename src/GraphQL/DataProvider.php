<?php declare(strict_types=1);

namespace App\GraphQL;

use Psr\Log\LoggerInterface;

class DataProvider
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    private $authors =
        [
            [
                'id' => '1',
                'name' => 'Dave',
            ],
            [
                'id' => '2',
                'name' => 'Rob',
            ]
        ];

    private $articles = [
        [
            'id' => '1',
            'title' => 'Dave\'s Article',
            'content' => 'All about Dave',
            'authorId' => '1'
        ],
        [
            'id' => '2',
            'title' => 'Another',
            'content' => 'Something',
            'authorId' => '1'
        ],
        [
            'id' => '3',
            'title' => 'Rob\'s Article',
            'content' => 'All about Rob',
            'authorId' => '2'
        ]
    ];

    public function findAuthorById($id)
    {
        $this->logger->debug('findAuthorById ' . $id);

        foreach ($this->authors as $author) {
            if ($author['id'] === $id) {
                return $author;
            }
        }

        return null;
    }

    public function findAuthorsById($ids)
    {
        return array_filter($this->authors, function($author) use ($ids){
            return in_array($author['id'], $ids, false);
        });
    }

    public function findArticlesByAuthorId($authorId)
    {
        $this->logger->debug('findArticlesByAuthorId ' . $authorId);

        $matches = array_filter($this->articles, function($article) use ($authorId){
            return $article['authorId'] === $authorId;
        });

        return $matches;
    }

    public function allAuthors(): array
    {
        $this->logger->debug('allAuthors');
        return $this->authors;
    }

    public function allArticles(): array
    {
        $this->logger->debug('allArticles');
        return $this->articles;
    }
}