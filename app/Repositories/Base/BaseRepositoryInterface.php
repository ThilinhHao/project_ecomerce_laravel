<?php

namespace App\Repositories\Base;

interface BaseRepositoryInterface
{
    /**
     * getModel
     * @return Model
    */
    public function getModel();

    /**
     * Count list of models.
     *
     * @return bool
    */
    public function count();

    /**
     * find model.
     * @return int $id
    */
    public function find($id);

    /**
     * find model.
     * @return int $id
    */
    public function findOrFail($id);

    /**
     * Get list of models with pagination.
     *
     * @param array $condition
     * @param $columns
     * @return LengthAwarePaginator
    */
    public function getListPagination();

    /**
     * Get list of models.
     * @return Builder[]|Collection
    */
    public function getList();

    /**
     * Get the model detail.
     * @return Model
    */
    public function getDetail();

    /**
     * Create model.
     * @param array $values
     * @return Model
    */
    public function create(array $values);

    /**
     * Update model.
     * @return int $id
     * @param array $values
    */
    public function update($id, array $values);

    /**
     * Delete model.
     * @return int $id
    */
    public function delete($id);

    public function paginateWithOrder($perPage, $orderBy, $orderDirection);

    public function getCountBySlug($slug);

    public function getFirstSlug($slug);
}
