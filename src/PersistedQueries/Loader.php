<?php


namespace Nuwave\Lighthouse\PersistedQueries;


use GraphQL\Server\OperationParams;
use Illuminate\Contracts\Cache\Repository as Cache;

class Loader implements LoaderInterface
{
    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var array|null
     */
    protected $schema;

    /**
     * @var bool
     */
    protected $apq = false;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;

        if (config('lighthouse.persisted.enable', false)) {
            $this->apq = config('lighthouse.persisted.apq', false);
            $this->schema = $this->loadSchemaFile(config('lighthouse.persisted.schema', null));
        } else {
            // Tell the client persisted queries are not supported
            response()->json([
                'errors' => [
                    'message' => 'PersistedQueryNotSupported'
                ]
            ]);
        }
    }

    public final function __invoke(string $queryId, OperationParams $params): string
    {
        $persistedQuery = $this->loadQuery($queryId);

        // Query not yet persisted and APQ is enabled
        if (!$persistedQuery && $this->apq) {
            if (!$params->query) {
                // Tell the client to send the query and the queryId again
                response()->json([
                    'errors' => [
                        'message' => 'PersistedQueryNotFound'
                    ]
                ]);
            }
            $this->persistQuery($queryId, $params->query);
        }

        return $persistedQuery ?? $params->query;
    }

    protected function loadQuery(string $queryId): ?string
    {
        if ($this->apq && ($query = $this->cache->get($this->getQueryCacheKey($queryId)))) {
            return $query;
        }

        if ($this->schema) {
            // TODO: How to get query from Apollo Schema File?
        }

        return null;
    }

    protected function persistQuery(string $queryId, string $query): void
    {
        $this->cache->add($this->getQueryCacheKey($queryId), $query, config('lighthouse.persisted.ttl', 3600));
    }

    protected function loadSchemaFile(?string $schemaPath): ?array
    {
        if ($schemaPath) {
            return json_decode(file_get_contents($schemaPath), true);
        }

        return null;
    }

    protected function getQueryCacheKey(string $queryId): string
    {
        return "apq.$queryId";
    }
}
