<?php

namespace Kuato\Generators;

use Kuato\Contracts\Generators\StubProcessorServiceInterface;

class StubProcessorService implements StubProcessorServiceInterface
{
    protected $stub;
    protected $moduleName;
    protected $subModuleName;

    /**
     * Factory method to create new instances of self
     *
     * @param  string $stub
     * @return $this
     */
    public function make($stub, $moduleName, $subModuleName=null)
    {
        // @todo Maybe use interface here...
        return app(self::class)->setStub($stub)
                               ->setModuleName($moduleName)
                               ->setSubModuleName($subModuleName);
    }

    /**
     * Setter for the working stub
     *
     * @param  string $stub
     * @return $this
     */
    public function setStub($stub)
    {
        $this->stub = $stub;

        return $this;
    }

    /**
     * Getter for the working stub
     *
     * @return string
     */
    public function getStub()
    {
        return $this->stub;
    }

    /**
     * Syntax sugar
     *
     * @return string
     */
    public function get()
    {
        return $this->getStub();
    }

    /**
     * Setter for the working module name
     *
     * @param  string $moduleName
     * @return $this
     */
    public function setModuleName($moduleName)
    {
        $this->moduleName = $moduleName;

        return $this;
    }

    /**
     * Getter for the working module name
     *
     * @return string
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }

    /**
     * Setter for the working sub-module name
     *
     * @param  string $subModuleName
     * @return $this
     */
    public function setSubModuleName($subModuleName)
    {
        $this->subModuleName = $subModuleName;

        return $this;
    }

    /**
     * Getter for the working sub-module name
     *
     * @return string
     */
    public function getSubModuleName()
    {
        return $this->subModuleName;
    }

    /**
     * Check to see if we're creating a submodule
     *
     * @return boolean
     */
    protected function isSubModule()
    {
        return !empty($this->subModuleName);
    }

    /**
     * Replace the module name in the stub i.e. User
     *
     * @param  string $stub
     * @return $this
     */
    public function replaceModuleName()
    {
        $moduleName = ucwords(camel_case($this->moduleName));
        $this->stub = str_replace('{{module}}', $moduleName, $this->stub);

        return $this;
    }

    /**
     * Replace the module name in the stub with plural i.e. Users
     *
     * @param  string $stub
     * @return $this
     */
    public function replaceModuleNamePlural()
    {
        $moduleName = str_plural(ucwords(camel_case($this->moduleName)));
        $this->stub = str_replace('{{modulePlural}}', $moduleName, $this->stub);

        return $this;
    }

    /**
     * Replace the class name in the stub i.e. User
     *
     * @param  string $stub
     * @return $this
     */
    public function replaceClassName()
    {
        $className = ($this->isSubModule()) ? $this->subModuleName : $this->moduleName;
        $className = ucwords(camel_case($className));

        $this->stub = str_replace('{{class}}', $className, $this->stub);

        return $this;
    }

    /**
     * Replace the class name in the stub with plural i.e. Users
     *
     * @param  string $stub
     * @return $this
     */
    public function replaceClassNamePlural()
    {
        $className = ($this->isSubModule()) ? $this->subModuleName : $this->moduleName;
        $className = str_plural(ucwords(camel_case($className)));

        $this->stub = str_replace('{{classPlural}}', $className, $this->stub);

        return $this;
    }

    /**
     * Replace the class name in the stub with lower case i.e. user
     *
     * @param  string $stub
     * @return $this
     */
    public function replaceClassNameLower()
    {
        $className = ($this->isSubModule()) ? $this->subModuleName : $this->moduleName;
        $className = strtolower($className);

        $this->stub = str_replace('{{classLowercase}}', $className, $this->stub);

        return $this;
    }

    /**
     * Replace the class name in the stub with lower case plural i.e. users
     *
     * @param  string $stub
     * @return $this
     */
    public function replaceClassNameLowerPlural()
    {
        $className = ($this->isSubModule()) ? $this->subModuleName : $this->moduleName;
        $className = str_plural(strtolower($className));

        $this->stub = str_replace('{{classLowercasePlural}}', $className, $this->stub);

        return $this;
    }

    /**
     * Syntactic sugar - runs all replace methods
     *
     * @return $this
     */
    public function replaceAll()
    {
        $methods = get_class_methods($this);

        foreach ($methods as $method) {
            if (substr($method, 0, 7) == 'replace' && !in_array($method, ['replaceAll', 'make'])) $this->$method();
        }

        return $this;
    }
}