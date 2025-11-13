<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FunctionalTestCase extends WebTestCase
{
    protected string $username = 'admin@example.com';

    protected ?KernelBrowser $client;

    protected ?User $user = null;

    public function setUp(): void
    {
        $this->client = static::createClient();

        $this->user = $this->getUser($this->username);

        $user = static::getContainer()->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy(['email' => 'admin@example.com']);

        $this->client->loginUser($user);
    }

    public function get(string $uri, int $expectedResponseCode = Response::HTTP_OK, ?array $server = null): array
    {
        $this->client->request(
            method: Request::METHOD_GET,
            uri: $uri,
            server: $server ?? $this->getAuthorizationHeaderWithToken(),
        );

        return $this->handleResponse($uri, Request::METHOD_GET, $expectedResponseCode);
    }

    /**
     * @param array<string, UploadedFile> $files
     *
     * @throws \JsonException
     */
    public function post(
        string $uri,
        array|string $data = '',
        int $expectedResponseCode = Response::HTTP_CREATED,
        ?array $server = null,
        array $files = []
    ): array {
        $this->client->request(
            method: Request::METHOD_POST,
            uri: $uri,
            files: $files,
            server: $server ?? $this->getAuthorizationHeaderWithToken(),
            content: is_string($data) ? $data : json_encode($data),
        );

        return $this->handleResponse($uri, Request::METHOD_POST, $expectedResponseCode);
    }

    private function handleResponse(string $uri, string $method, $expectedResponseCode): array
    {
        $response = $this->client->getResponse();

        $this->assertSame($expectedResponseCode, $response->getStatusCode());

        return $response->getContent() ?
            json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR) : [];
    }

    protected function getAuthorizationHeaderWithToken(?string $accessToken = null): array
    {
        return [
            'HTTP_AUTHORIZATION' => 'Bearer '.($accessToken ?? $this->getAuthorizationToken()),
            'CONTENT_TYPE' => 'application/json',
        ];
    }

    protected function getAuthorizationToken(): string
    {
        $container = static::getContainer();

        return $container->get('lexik_jwt_authentication.jwt_manager')->createFromPayload($this->user);
    }

    protected function getUser(string $username): ?User
    {
        $container = static::getContainer();

        $em = $container->get(EntityManagerInterface::class);

        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy(['email' => $username]);

        return $user;
    }
}