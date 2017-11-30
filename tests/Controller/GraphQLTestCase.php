<?php declare(strict_types=1);

namespace App\Tests\Controller;

use App\DataFixtures\AppFixtures;
use App\Tests\GraphQLTestClient;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\DataFixtures\Loader;

class GraphQLTestCase extends WebTestCase
{
    /** @var GraphQLTestClient */
    protected $client;

    public function setUp(): void
    {
        $this->client = new GraphQLTestClient(static::createClient());
        $this->refreshDatabaseSchema();
        $this->loadFixtures();
    }

    protected function loadFixtures()
    {
        $loader = new Loader();
        $loader->addFixture(new AppFixtures());

        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->getEntityManager(), $purger);
        $executor->execute($loader->getFixtures());
    }

    protected function refreshDatabaseSchema()
    {
        $em = $this->getEntityManager();

        $metadata = $em->getMetadataFactory()->getAllMetadata();
        if (!empty($metadata)) {
            $tool = new SchemaTool($em);
            $tool->updateSchema($metadata);
        }
    }

    protected function getRepository(string $className)
    {
        $em = $this->getEntityManager();

        return $em->getRepository($className);
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->getService('doctrine.orm.entity_manager');
    }

    protected function getService(string $serviceName)
    {
        return static::$kernel->getContainer()->get($serviceName);
    }

    protected function assertDefaultHeaders(Response $response): void
    {
        $this->assertEquals('application/json', $response->headers->get('content-type'));
        $this->assertEquals('no-cache, private', $response->headers->get('cache-control'));
        $this->assertNotNull($response->headers->get('date'));
    }

    protected function assertStatusCode(int $expectedStatusCode, Response $response): void
    {
        $this->assertEquals($expectedStatusCode, $response->getStatusCode(), 'Status code did not match');
    }

    protected function decodeResponse(Response $response): array
    {
        $decoded = json_decode($response->getContent(), true);

        if ($decoded === null) {
            $message = sprintf('Error decoding JSON: %s %s', json_last_error(), json_last_error_msg());
            throw new \RuntimeException($message);
        }

        return $decoded;
    }

    protected function seeJsonStructure(array $structure, array $responseData)
    {
        foreach ($structure as $key => $value) {
            if (is_array($value) && $key === '*') {
                $this->assertInternalType('array', $responseData);
                foreach ($responseData as $responseDataItem) {
                    $this->seeJsonStructure($structure['*'], $responseDataItem);
                }
            } elseif (is_array($value)) {
                $this->assertArrayHasKey($key, $responseData);
                $this->seeJsonStructure($structure[$key], $responseData[$key]);
            } else {
                $this->assertArrayHasKey($value, $responseData);
            }
        }

        return $this;
    }

    protected function assertResponseHasErrors(Response $response)
    {
        $this->seeJsonStructure([
            'errors' => [
                '*' => [
                    'message',
                    'category',
                ]
            ]
        ], $this->decodeResponse($response));
    }

    protected function assertErrorMessagePresent(string $message, string $category, Response $response)
    {
        $decodedResponse = $this->decodeResponse($response);
        $this->assertContains(
            [
                'message' => $message,
                'category' => $category,
            ],
            $decodedResponse['errors']
        );

    }

    protected function assertContentType(string $expectedContentType, Response $response)
    {
        $this->assertEquals($expectedContentType, $response->headers->get('content-type'));
    }
}