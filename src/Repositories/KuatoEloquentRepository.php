<?php 

namespace Kuato\Repositories;

use Bosnadev\Repositories\Eloquent\Repository as BosnadevRepository;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class KuatoEloquentRepository extends BosnadevRepository implements KuatoRepository
{
    public function model()
    {
        return $this->useModel();
    }

    abstract public function useModel();

    public function paginate($perPage = 1, $extraFields = [], $columns = array('*'))
    {
        $paginatedModels = parent::paginate($perPage, $columns);
        $paginatedModels->appends($extraFields);

        $this->resetCriteria();

        return $paginatedModels;
    }

    public function resetCriteria()
    {
        $this->criteria = new Collection;
        $this->makeModel();
        return $this;
    }

    public function first($columns = ['*'])
    {
        $this->applyCriteria();
        $result = $this->model->first($columns);
        $this->resetCriteria();

        return $result;
    }

    public function deleteAll()
    {
        $this->applyCriteria();
        $result = $this->model->delete();
        $this->resetCriteria();

        return $result;
    }

    public function updateMany(array $data, array $ids, $attribute='id')
    {
        return $this->model->whereIn($attribute, $ids)->update($data);
    }

    public function all($columns = ['*'])
    {
        $result = parent::all($columns);
        $this->resetCriteria();

        return $result;
    }

    public function lists($value, $key = null)
    {
        $result = parent::lists($value, $key);
        $this->resetCriteria();

        return $result;
    }

    public function create(array $data)
    {
        $this->resetCriteria();
        return parent::create($data);
    }

    public function update(array $data, $id)
    {
        $this->resetCriteria();
        return parent::update($data, $id);
    }

    public function delete($id)
    {
        $this->resetCriteria();
        $result = parent::delete($id);

        return $result;
    }

    public function find($id, $columns = ['*'])
    {
        $result = parent::find($id, $columns);
        $this->resetCriteria();

        return $result;
    }

    public function findBy($field, $value, $columns = ['*'])
    {
        return parent::findBy($column, $value, $columns);
    }

    public function persist(Model $model)
    {
        return $model->save();
    }

    public function updateSubset(array $data, array $ids)
    {
        return $this->model->whereIn('id', $ids)->update($data);
    }
}
