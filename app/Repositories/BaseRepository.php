<?php

namespace App\Repositories;

use App\Repositories\Contracts\BaseInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;


abstract class BaseRepository implements BaseInterface
{
    /**      
     * @var Model      
     */
    protected $model;

    /**      
     * @var bool
     */
    private $isSet;

    /**      
     * BaseRepository constructor.      
     *      
     * @param Model $model      
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->isSet = false;
    }

    /**
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * @param Model $model
     * @return BaseRepository
     */
    public function setModel($model): static
    {
        $this->model = $model;
        $this->isSet = true;

        return $this;
    }

    /**
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function all(array $columns = ['*'], array $relations = []): Collection
    {
        return $this->model->with($relations)->get($columns);
    }

    /**
     * @param int $perPage
     * @param array $columns
     * @param array $relations
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = []): LengthAwarePaginator
    {
        return $this->model->with($relations)->paginate($perPage, $columns);
    }

    /**
     * Get all trashed models.
     *
     * @return Collection
     */
    public function allTrashed(): Collection
    {
        return $this->model->onlyTrashed()->get();
    }

    /**
     * Find model by specified column name.
     *
     * @param string $columnName
     * @param int $id
     * @param array $columns
     * @param array $relations
     * @param array $appends
     * @return Model
     */
    public function findBy(string $columnName, mixed $id, array $columns = ['*'], array $relations = [], array $appends = []): ?Model
    {
        return $this->model->select($columns)->with($relations)->where($columnName, $id)->first()->append($appends);
    }

    /**
     * Find model by id.
     *
     * @param int|string $id
     * @param array $columns
     * @param array $relations
     * @param array $appends
     * @return Model
     */
    public function findById(int|string $id, array $columns = ['*'], array $relations = [], array $appends = []): ?Model
    {
        return $this->model->select($columns)->with($relations)->findOrFail($id)->append($appends);
    }

    /**
     * Find trashed model by id.
     *
     * @param int|string $id
     * @return Model
     */
    public function findTrashedById(int|string $id): ?Model
    {
        return $this->model->withTrashed()->findOrFail($id);
    }

    /**
     * Find only trashed model by id.
     *
     * @param int|string $id
     * @return Model
     */
    public function findOnlyTrashedById(int|string $id): ?Model
    {
        return $this->model->onlyTrashed()->findOrFail($id);
    }

    /**
     * Create a model.
     *
     * @param array $payload
     * @return Model
     */
    public function create(array $payload): ?Model
    {
        $model = $this->model->create($payload);

        return $model->fresh();
    }

    /**
     * Update existing model.
     *
     * @param array $payload
     * @param string $columnName
     * @param int|string $id
     * @return bool
     */
    public function update(array $payload, string $columnName = 'id', int|string $id = null): bool
    {
        if (!$id) {

            if ($this->isSet) {
                return $this->model->update($payload);
            }

            throw new \LogicException('The base model has not been defined via the "setModel" method.');
        } else {

            $model = $this->findBy($columnName, $id);

            return $model->update($payload);
        }
    }

    /**
     * Save the model to the database.
     *
     * @param  array  $options
     * @return bool
     */
    public function save(): bool
    {
        if ($this->isSet) {
            return $this->model->save();
        }

        throw new \LogicException('The base model has not been defined via the "setModel" method.');
    }

    /**
     * Save the model to the database without raising any events.
     *
     * @param  array  $options
     * @return bool
     */
    public function saveQuietly(): bool
    {
        if ($this->isSet) {
            return $this->model->saveQuietly();
        }

        throw new \LogicException('The base model has not been defined via the "setModel" method.');
    }

    /**
     * Delete the model from the database.
     *
     * @return bool
     */
    public function delete(): bool
    {
        if ($this->isSet) {
            return $this->model->delete();
        }

        throw new \LogicException('The base model has not been defined via the "setModel" method.');
    }

    /**
     * Delete model by id.
     *
     * @param int|string $id
     * @return bool
     */
    public function deleteById(int|string $id): bool
    {
        return $this->findById($id)->delete();
    }

    /**
     * Permanently delete model by id.
     *
     * @param int|string $id
     * @return bool
     */
    public function permanentlyDeleteById(int|string $id): bool
    {
        return $this->findTrashedById($id)->forceDelete();
    }

    /**
     * Destroy the models for the given IDs.
     *
     * @param  \Illuminate\Support\Collection|array|int|string  $ids
     * @return int
     */
    public function destroy($ids): int
    {
        return $this->model->destroy($ids);
    }

    /**
     * Restore model by id.
     *
     * @param int|string $id
     * @return bool
     */
    public function restoreById(int|string $id): bool
    {
        return $this->findOnlyTrashedById($id)->restore();
    }

    /**
     * Eager Loading Relationships.
     *
     * @param mixed $relations
     * @return Collection
     */
    public function with(mixed $relations): Collection
    {
        if ($this->isSet) {
            return $this->model->with($relations)->get();
        }

        throw new \LogicException('The base model has not been defined via the "setModel" method.');
    }

    /**
     * Lazy Eager Loading Relationships.
     *
     * @param mixed $relations
     * @return Model
     */
    public function load(mixed $relations): Model
    {
        if ($this->isSet) {
            return $this->model->load($relations);
        }

        throw new \LogicException('The base model has not been defined via the "setModel" method.');
    }

    /* #REFERENCES : 
        - https://asperbrothers.com/blog/implement-repository-pattern-in-laravel/
        - https://dev.to/carlomigueldy/getting-started-with-repository-pattern-in-laravel-using-inheritance-and-dependency-injection-2ohe
    */
}
