<?php declare(strict_types=1);


namespace App\Repository;


class AuthorRepository
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

    public function findAuthorById($id)
    {
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

    public function allAuthors(): array
    {
        return $this->authors;
    }
}