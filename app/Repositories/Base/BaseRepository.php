<?php

namespace App\Repositories\Base;

use App\Http\Constants\PaginateConstant;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;
    protected string $modelTable;

    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->modelTable = $model->getTable();
    }

    public function getModel()
    {
        return $this->model;
    }

    public function count()
    {
        return $this->model->count();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function findOrFail($id)
    {
        return $this->model->findOrFail($id);
    }

    public function getListPagination()
    {
        return $this->model->paginate(PaginateConstant::PERPAGE);
    }

    public function getList()
    {
        return $this->model->all();
    }

    public function getDetail()
    {
        return $this->model->first();
    }

    public function create(array $values)
    {
        return $this->model->create($values);
    }

    public function update($id, array $values)
    {
        $record = $this->model->findOrFail($id);

        return $record->update($values);
    }

    public function delete($id)
    {
        $record = $this->model->findOrFail($id);
        return  $record->delete($id);
    }

    public function paginateWithOrder($perPage, $orderBy = 'id', $orderDirection = 'DESC')
    {
        return $this->model
            ->orderBy($orderBy, $orderDirection)
            ->paginate($perPage);
    }

    public function getCountBySlug($slug)
    {
        return $this->model
            ->where('slug', $slug)
            ->count();
    }

    public function getFirstSlug($slug)
    {
        return $this->model->where('slug', $slug)->first();
    }
}
