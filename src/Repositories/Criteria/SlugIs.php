<?php

namespace Kuato\Repositories\Criteria;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;

class SlugIs extends Criteria
{
    protected $slug;

    public function __construct($slug)
    {
        $this->slug = $slug;
    }

    public function apply($model, Repository $repo)
    {
        $model = $model->where('slug', '=', $this->slug);

        return $model;
    }
}