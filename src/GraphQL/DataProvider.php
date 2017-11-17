<?php declare(strict_types=1);

namespace App\GraphQL;

class DataProvider
{
    public static $authors =
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

    public static $articles = [
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

    public static function findAuthorById($id)
    {
        foreach (static::$authors as $author) {
            if ($author['id'] === $id) {
                return $author;
            }
        }

        return null;
    }

    public static function findArticlesByAuthorId($authorId)
    {
        $matches = array_filter(static::$articles, function($article) use ($authorId){
            return $article['authorId'] === $authorId;
        });

        return $matches;
    }

    public static function allAuthors(): array
    {
        return static::$authors;
    }

    public static function allArticles(): array
    {
        return static::$articles;
    }
}