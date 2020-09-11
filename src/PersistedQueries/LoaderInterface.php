<?php


namespace Nuwave\Lighthouse\PersistedQueries;


use GraphQL\Server\OperationParams;

interface LoaderInterface
{
    function __invoke(string $queryId, OperationParams $params): string;
}
