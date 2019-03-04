<?php

namespace Kuato\Repositories\Criteria;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;

class UpdatedBefore extends Criteria
{
    protected $date;

    public function __construct($date)
    {
        $this->date = $date;
    }

    public function apply($model, Repository $repo)
    {
        $model = $model->where('updated_at', '<=', $this->date);
        return $model;
    }
}