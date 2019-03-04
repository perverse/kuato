<?php 

namespace Kuato\Repositories;

interface KuatoRepository
{
    /**
     * 
     * @param array 	$columns
     *
     * @return collection
     */
	public function all($columns = ['*']);

    /**
     * 
     * @param array     $params
     * @param array     $columns
     *
     * @return collection
     */
	public function paginate($perPage = 1, $extraFields = [], $columns = array('*'));

    /**
     * 
     * @param int|string 	   $id
     * @param array 	       $columns
     *
     * @return model
     */
	public function find($id, $columns = ['*']);

    /**
     * 
     * @param string     $column
     * @param mixed      $value
     * @param array      $columns
     *
     * @return model
     */
    public function findBy($column, $value, $columns = ['*']);

    /**
     * 
     * @param array 	$data
     *
     * @return model
     */
	public function create(array $data);

    /**
     * 
     * @param int       $id
     * @param array 	$data
     *
     * @return model
     */
	public function update(array $data, $id);

    /**
     * 
     * @param int 	    $id
     *
     * @return model
     */
	public function delete($id);
}
