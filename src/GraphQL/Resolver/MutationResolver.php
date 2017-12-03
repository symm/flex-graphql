<?php

declare(strict_types=1);

namespace App\GraphQL\Resolver;

use App\Entity\Article;
use App\Entity\Author;
use Doctrine\ORM\EntityManagerInterface;
use GraphQL\Type\Definition\ResolveInfo;

class MutationResolver implements Resolver
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke($root, $args, $context, ResolveInfo $info)
    {
        if ('createArticle' === $info->fieldName) {
            return $this->createArticle($context['user'], $args['input']['title'], $args['input']['content']);
        }
    }

    private function createArticle(Author $author, string $title, string $content)
    {
        $article = new Article($author, $title, $content);

        $this->em->persist($article);
        $this->em->flush();

        return [
            'article' => [
                'id' => $article->getId()->toString(),
                'title' => $article->getTitle(),
                'content' => $article->getContent(),
            ],
        ];
    }
}
