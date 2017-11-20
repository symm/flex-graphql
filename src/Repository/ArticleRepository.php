<?php declare(strict_types=1);

namespace App\Repository;

class ArticleRepository
{
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

    public function findArticlesByAuthorId($authorId)
    {
        return array_filter($this->articles, function($article) use ($authorId){
            return $article['authorId'] === $authorId;
        });
    }


    public function allArticles(): array
    {
        return $this->articles;
    }
}