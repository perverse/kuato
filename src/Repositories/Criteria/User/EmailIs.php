<?php

namespace Kuato\Repositories\Criteria\User;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;

class EmailIs extends Criteria
{
    protected $email;

    public function __construct($email)
    {
        $this->email = $email;
    }

    public function apply($model, Repository $repo)
    {
        $model = $model->where('email', '=', $this->email);
        return $model;
    }
}