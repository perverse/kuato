<?php

namespace Kuato\Repositories\Criteria\User;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;

class IncludeUser extends Criteria
{
    public function apply($model, Repository $repo)
    {
        return $model->with('user');
    }
}