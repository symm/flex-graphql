<?php declare(strict_types=1);

namespace App\GraphQL;

class DataProvider
{
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
            'title' => 'Rob\'s Article',
            'content' => 'All about Rob',
            'authorId' => '2'
        ]
    ];

    public function findAuthorById($id)
    {
        foreach ($this->authors as $author) {
            if ($author['id'] === $id) {
                return $author;
            }
        }

        return null;
    }

    public function findArticlesByAuthorId($authorId)
    {
        $matches = array_filter($this->articles, function($article) use ($authorId){
            return $article['authorId'] === $authorId;
        });

        return $matches;
    }

    public function allAuthors(): array
    {
        return $this->authors;
    }

    public function allArticles(): array
    {
        return $this->articles;
    }
}