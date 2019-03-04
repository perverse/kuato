<?php 

namespace Kuato\Repositories;

use Cache;

abstract class KuatoCacheDecorator implements KuatoRepository
{
    /**
     * @var \Modules\Core\Repositories\BaseRepository   $repository
     */
    protected $repository;
    
    /**
     * @var \Illuminate\Cache\Repository                $cache
     */
    protected $cache;

    /**
     * @var string                                      $entityName
     */
    protected $entityName;

    /**
     * @var int                                         $cacheTime
     */
    protected $cacheTime;

    /**
     *
     * @return void
     */
    public function __construct()
    {
        $this->cache = app('Illuminate\Cache\Repository');
        $this->cacheTime = 1;
    }

    /**
     *
     * @param  array    $columns
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(array $columns = ['*'])
    {
        return $this->cache->remember("{$this->entityName}:all", $this->cacheTime, function() {
            return $this->repository->all();
        });
    }

    /**
     *
     * @param array     $params
     * @param array     $columns
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function paginate(array $params, array $columns = ['*'])
    {
        return $this->cache->tags($this->entityName)->remember("{$this->entityName}:paginate:{$this->generateKey($params)}", $this->cacheTime, function() use($params, $columns) {
            return $this->repository->paginate($params, $columns);
        });
    }

    /**
     *
     * @param  int|string       $id
     * @param  array            $columns
     *
     * @return mixed
     */
    public function find($id, array $columns = ['*'])
    {
        return $this->cache->tags($this->entityName)->remember("{$this->entityName}:find:{$id}", $this->cacheTime, function() use($id) {
            return $this->repository->find($id);
        });
    }

    /**
     *
     * @param string     $column
     * @param mixed      $value
     * @param array      $columns
     *
     * @return mixed
     */
    public function findBy(string $column, $value, array $columns = ['*'])
    {
        return $this->cache->tags($this->entityName)->remember("{$this->entityName}:findBy:{$column}:{$value}", $this->cacheTime, function() use($column, $value) {
            return $this->repository->findBy($column, $value);
        });
    }

    /**
     *
     * @param array     $data
     *
     * @return mixed
     */
    public function create(array $data)
    {
        $this->cache->tags($this->entityName)->flush();
        return $this->repository->create($data);
    }

    /**
     *
     * @param int       $id
     * @param array     $data
     * 
     * @return mixed
     */
    public function update(int $id, array $data)
    {
        $this->cache->tags($this->entityName)->flush();
        return $this->repository->update($id, $data);
    }

    /**
     *
     * @param int     $id
     *
     * @return mixed
     */
    public function delete(int $id)
    {
        $this->cache->tags($this->entityName)->flush();
        return $this->repository->delete($id);
    }

    /**
     * 
     * @return bool
     */
    public function clearCache()
    {
        return $this->cache->tags($this->entityName)->flush();
    }

    /**
     * 
     * @param array     $data
     *
     * @return bool
     */
    public function generateKey(array $data)
    {
        return md5(implode('', array_map(
            function ($v, $k) { 
                return sprintf("%s='%s'", $k, $v); 
            },
            $data,
            array_keys($data)
        )));
    }
}
