<?php

namespace App\Modules\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

abstract class BaseService
{
    protected $model;

    /**
     * Get all records
     */
    public function getAll(array $filters = []): Collection
    {
        $query = $this->model->newQuery();
        
        foreach ($filters as $key => $value) {
            if ($value !== null) {
                $query->where($key, $value);
            }
        }

        return $query->get();
    }

    /**
     * Get paginated records
     */
    public function getPaginated(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->newQuery();
        
        foreach ($filters as $key => $value) {
            if ($value !== null) {
                if ($key === 'search') {
                    $this->applySearch($query, $value);
                } else {
                    $query->where($key, $value);
                }
            }
        }

        return $query->paginate($perPage);
    }

    /**
     * Find record by ID
     */
    public function findById($id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * Find record by ID or fail
     */
    public function findByIdOrFail($id): Model
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Create new record
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update record
     */
    public function update($id, array $data): Model
    {
        $record = $this->findByIdOrFail($id);
        $record->update($data);
        return $record->fresh();
    }

    /**
     * Delete record
     */
    public function delete($id): bool
    {
        $record = $this->findByIdOrFail($id);
        return $record->delete();
    }

    /**
     * Apply search filters (override in child classes)
     */
    protected function applySearch($query, string $search): void
    {
        // Override in child classes for specific search logic
    }
}
