<?php 

namespace Kuato\Validators;

abstract class Validator {

    /**
     *
     * @var array
     */
    protected $input;

    /**
     *
     * @var array
     */
    protected $errors;
    
    /**
     *
     * @param array         $input
     *
     * @return \App\Kuato\Validators\Validator
     */
    public function with(array $input)
    {
        $this->input = $input;
        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function passes()
    {
        $validation = \Validator::make($this->input, static::rules(), static::messages());
       
        if ( $validation->passes() ):
            return true;
        endif;

        $this->errors = $validation->messages();
        
        return false;
    }

    /**
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

}