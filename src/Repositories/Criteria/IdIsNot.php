<?php

namespace Kuato\Repositories\Criteria;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;

class IdIsNot extends Criteria
{
    protected $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function apply($model, Repository $repo)
    {
        $model = $model->where('id', '!=', $this->id);
        return $model;
    }
}