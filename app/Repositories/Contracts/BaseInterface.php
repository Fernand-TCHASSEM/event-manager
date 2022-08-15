<?php

namespace App\Repositories\Contracts;

use App\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseInterface
{

    /**
     * @return Model
     */
    public function getModel(): Model;
    
    /**
     * @param Model $model
     * @return BaseRepository
     */
    public function setModel($model): static;

    /**
     * Get all models.
     *
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function all(array $columns = ['*'], array $relations = []): Collection;

    /**
     * @param int $perPage
     * @param array $columns
     * @param array $relations
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = []): LengthAwarePaginator;

    /**
     * Get all trashed models.
     *
     * @return Collection
     */
    public function allTrashed(): Collection;

    /**
     * Find model by specified column name.
     *
     * @param string $columnName
     * @param mixed $id
     * @param array $columns
     * @param array $relations
     * @param array $appends
     * @return Model
     */
    public function findBy(string $columnName, mixed $id, array $columns = ['*'], array $relations = [], array $appends = []): ?Model;

    /**
     * Find model by id.
     *
     * @param int|string $id
     * @param array $columns
     * @param array $relations
     * @param array $appends
     * @return Model
     */
    public function findById(
        int|string $id,
        array $columns = ['*'],
        array $relations = [],
        array $appends = []
    ): ?Model;

    /**
     * Find trashed model by id.
     *
     * @param int|string $id
     * @return Model
     */
    public function findTrashedById(int|string $id): ?Model;

    /**
     * Find only trashed model by id.
     *
     * @param int|string $id
     * @return Model
     */
    public function findOnlyTrashedById(int|string $id): ?Model;

    /**
     * Create a model.
     *
     * @param array $payload
     * @return Model
     */
    public function create(array $payload): ?Model;

    /**
     * Update existing model.
     *
     * @param array $payload
     * @param string $columnName
     * @param int|string $id
     * @return bool
     */
    public function update(array $payload, string $columnName = 'id', int|string $id = null): bool;

    /**
     * Save the model to the database.
     *
     * @param  array  $options
     * @return bool
     */
    public function save(): bool;

    /**
     * Save the model to the database without raising any events.
     *
     * @param  array  $options
     * @return bool
     */
    public function saveQuietly(): bool;

    /**
     * Delete the model from the database.
     *
     * @return bool
     */
    public function delete(): bool;

    /**
     * Delete model by id.
     *
     * @param int|string $id
     * @return bool
     */
    public function deleteById(int|string $id): bool;

    /**
     * Permanently delete model by id.
     *
     * @param int|string $id
     * @return bool
     */
    public function permanentlyDeleteById(int|string $id): bool;

    /**
     * Destroy the models for the given IDs.
     *
     * @param  \Illuminate\Support\Collection|array|int|string  $ids
     * @return int
     */
    public function destroy($ids): int;

    /**
     * Restore model by id.
     *
     * @param int|string $id
     * @return bool
     */
    public function restoreById(int|string $id): bool;

    /**
     * Eager Loading Relationships.
     *
     * @param mixed $relations
     * @return Collection
     */
    public function with(mixed $relations): Collection;

    /**
     * Lazy Eager Loading Relationships.
     *
     * @param mixed $relations
     * @return Model
     */
    public function load(mixed $relations): Model;
}
