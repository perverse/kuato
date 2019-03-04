<?php

namespace Kuato\Repositories\Criteria\User;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;

class BelongsTo extends Criteria
{
    protected $user_id;

    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    public function apply($model, Repository $repo)
    {
        $model = $model->where('user_id', '=', $this->user_id);
        return $model;
    }
}