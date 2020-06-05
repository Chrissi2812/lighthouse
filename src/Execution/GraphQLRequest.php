<?php

namespace Nuwave\Lighthouse\Execution;

interface GraphQLRequest
{
    /**
     * Get the contained GraphQL query string.
     */
    public function query(): string;

    /**
     * Get the given variables for the query.
     *
     * @return array<string, mixed>
     */
    public function variables(): array;

    /**
     * Get the operationName of the current request.
     */
    public function operationName(): ?string;

    /**
     * Is the current query a batched query?
     */
    public function isBatched(): bool;

    /**
     * Get additional info on the current request
     *
     * @return array
     */
    public function extensions(): array;

    /**
     * Is the current query hashed?
     *
     * @return bool
     */
    public function isHashed(): bool;

    /**
     * Get hash of the current request
     *
     * @return string|null
     */
    public function hash(): ?string;

    /**
     * Advance the batch index and indicate if there are more batches to process.
     */
    public function advanceBatchIndex(): bool;

    /**
     * Get the index of the current batch.
     *
     * Returns null if we are not resolving a batched query.
     */
    public function batchIndex(): ?int;
}
