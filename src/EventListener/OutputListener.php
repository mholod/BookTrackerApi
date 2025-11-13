<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Response\Output;
use Neomerx\JsonApi\Encoder\Encoder;
use Neomerx\JsonApi\Factories\Factory;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use App\Encoder\JsonApiSchema;

final class OutputListener
{
    /** @var array<string, class-string<JsonApiSchema>> */
    private array $schemas;

    /**
     * @param ServiceLocator<JsonApiSchema> $taggedSchemas
     */
    public function __construct(ServiceLocator $taggedSchemas)
    {
        $factory = new Factory();
        foreach ($taggedSchemas->getProvidedServices() as $id => $className) {
            if (is_subclass_of($className, JsonApiSchema::class)) {
                /** @var JsonApiSchema $schema */
                $schema = new $className($factory);
                $this->schemas[$schema->encodesResource()] = $className;
            }
        }
    }

    public function __invoke(ViewEvent $event): void
    {
        $result = $event->getControllerResult();

        if ($result instanceof Response || $result === null) {
            return;
        }

        $data = $result;
        $meta = null;
        $status = Response::HTTP_OK;

        if ($result instanceof Output) {
            $data = $result->getData();
            $meta = $result->getMeta();
            $status = $result->getResponseCode();
        }

        $encoder = Encoder::instance($this->schemas);
        $json = $encoder->encodeData($data);

        // include meta section if provided
        if ($meta !== null) {
            $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            $decoded['meta'] = $meta;
            $json = json_encode($decoded, JSON_THROW_ON_ERROR);
        }

        $event->setResponse(new JsonResponse($json, $status, [], true));
    }
}
