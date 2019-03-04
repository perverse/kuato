<?php

namespace Kuato\Generators;

use Kuato\Contracts\Generators\GeneratorServiceInterface;
use Kuato\Contracts\Generators\StubProcessorServiceInterface;
use Kuato\Exceptions\PathCreationException;
use Illuminate\Contracts\Container;
use Illuminate\Filesystem\Filesystem;
use Config;

class GeneratorService implements GeneratorServiceInterface
{
    protected $files;
    protected $composer;
    protected $stubs;

    public function __construct(Filesystem $files, StubProcessorServiceInterface $stub)
    {
        $this->files = $files;
        $this->composer = app('composer'); // @todo look up correct contract and switch this to auto injection
        $this->stubs = $stub;
    }

    /**
     * Create a new Kuato module
     *
     * @param  string $name
     * @return null
     */
    public function generate($name, $subname=null)
    {
        if ($this->files->exists($path = $this->getPath($name))):
            throw new PathCreationException('Module ' . $name . ' already exists!');
        endif;

        if (!empty($subname) && $this->files->exists($this->getPath($name . '/Entities/' . $subname . '.php'))):
            throw new PathCreationException('Submodule ' . $subname . ' already exists!');
        endif;
        
        $this->makeDirectories($path);
        
        $this->compileMigration($path, $name, $subname);
        $this->compileEntity($path, $name, $subname);
        $this->compileService($path, $name, $subname);
        $this->compileServiceInterface($path, $name, $subname);
        $this->compileTransformer($path, $name, $subname);
        //$this->compileValidator($path, $name, $subname);

        $this->compileServiceProvider($path, $name, $subname);
        $this->compileServiceProviderRoute($path, $name, $subname);

        $this->compileRepository($path, $name, $subname);
        $this->compileRepositoryEloquent($path, $name, $subname);
        
        $this->compileController($path, $name, $subname);
        $this->compileRoutes($path, $name, $subname);
        $this->compileApiRoutes($path, $name, $subname);
        $this->compileTest($path, $name, $subname);
        $this->compileModuleJson($path, $name, $subname);

        $this->composer->dumpAutoloads();
    }

    /**
     * Get the path where we should store the new module.
     *
     * @param  string $name
     * @return string
     */
    protected function getPath($name)
    {
        return base_path('app/Modules/' . $name);
    }

    /**
     * Get the path to our stubs
     *
     * @param  string $name
     * @return string
     */
    protected function getStubPath($stubName)
    {
        return base_path("kuato/Generators/stubs/" . $stubName);
    }

    /**
     * Build the directories for the module if necessary.
     *
     * @param  string $path
     * @return string
     */
    protected function makeDirectories($path)
    {
        $directories = [
            $path => [],
            $path . '/Controllers' => [],
            $path . '/Entities' => [],
            $path . '/Contracts' => [],
            $path . '/Contracts/Repositories' => [],
            $path . '/Contracts/Services' => [],
            $path . '/Providers' => [],
            $path . '/Repositories' => [],
            $path . '/Transformers' => [],
            // $path . '/Validators' => [],
            $path . '/Services' => [],
        ];

        $default_opts = [
            'mask' => 0777,
            'recursive' => true,
            'force' => true
        ];

        foreach ($directories as $directory => $opts) {
            $opts = array_merge($default_opts, $opts);

            if ( ! $this->files->isDirectory($directory) ):
                $this->files->makeDirectory($directory, $opts['mask'], $opts['recursive'], $opts['force']);
            endif;
        }
    }

    /**
     * Create the database migration file.
     *
     * @param  string $path
     * @param  string $name
     * @return null
     */
    protected function compileMigration($path, $name, $subname)
    {
        //$name = str_plural(strtolower($name));

        $stub = $this->files->get($this->getStubPath('migration_create.stub'));
        $stub = $this->stubs->make($stub, $name, $subname)
                            ->replaceAll()
                            ->get();

        $this->files->put(base_path("database/migrations/" . date('Y_m_d_His') . '_create_' . strtolower($name) . "_table.php"), $stub);
    }

    /**
     * Create the entity file.
     *
     * @param  string $path
     * @param  string $name
     * @return binary
     */
    protected function compileEntity($path, $name, $subname)
    {
        $stub = $this->files->get($this->getStubPath('entity.stub'));
        $stub = $this->stubs->make($stub, $name, $subname)
                            ->replaceAll()
                            ->get();

        $this->files->put($path . "/Entities/" . $name . ".php", $stub);
    }

    /**
     * Create the service file.
     *
     * @param  string $path
     * @param  string $name
     * @return binary
     */
    protected function compileService($path, $name, $subname)
    {
        $stub = $this->files->get($this->getStubPath('service.stub'));
        $stub = $this->stubs->make($stub, $name, $subname)
                            ->replaceAll()
                            ->get();

        $this->files->put($path . "/Services/" . $name . "Service.php", $stub);
    }

    /**
     * Create the service interface file.
     *
     * @param  string $path
     * @param  string $name
     * @return binary
     */
    protected function compileServiceInterface($path, $name, $subname)
    {
        $stub = $this->files->get($this->getStubPath('service_interface.stub'));
        $stub = $this->stubs->make($stub, $name, $subname)
                            ->replaceAll()
                            ->get();

        $this->files->put($path . "/Contracts/Services/" . $name . "ServiceInterface.php", $stub);
    }

    /**
     * Create the transformer file.
     *
     * @param  string $path
     * @param  string $name
     * @return binary
     */
    protected function compileTransformer($path, $name, $subname)
    {
        $stub = $this->files->get($this->getStubPath('transformer.stub'));
        $stub = $this->stubs->make($stub, $name, $subname)
                            ->replaceAll()
                            ->get();

        $this->files->put($path . "/Transformers/" . $name . "Transformer.php", $stub);
    }

    /**
     * Create the validator file.
     *
     * @param  string $path
     * @param  string $name
     * @return binary
     */
    protected function compileValidator($path, $name, $subname)
    {
        $stub = $this->files->get($this->getStubPath('validator.stub'));
        $stub = $this->stubs->make($stub, $name, $subname)
                            ->replaceAll()
                            ->get();

        $this->files->put($path."/Validators/" . $name . "Validator.php", $stub);
    }

    /**
     * Create the service provider file.
     *
     * @param  string $path
     * @param  string $name
     * @return binary
     */
    protected function compileServiceProvider($path, $name, $subname)
    {
        $stub = $this->files->get($this->getStubPath('service_provider.stub'));
        $stub = $this->stubs->make($stub, $name, $subname)
                            ->replaceAll()
                            ->get();

        $this->files->put($path . "/Providers/" . $name . "ServiceProvider.php", $stub);
    }

    /**
     * Create the route service provider file.
     *
     * @param  string $path
     * @param  string $name
     * @return binary
     */
    protected function compileServiceProviderRoute($path, $name, $subname)
    {
        $stub = $this->files->get($this->getStubPath('service_provider_route.stub'));
        $stub = $this->stubs->make($stub, $name, $subname)
                            ->replaceAll()
                            ->get();

        $this->files->put($path . "/Providers/" . $name . "RouteServiceProvider.php", $stub);
    }

    /**
     * Create the repository file.
     *
     * @param  string $path
     * @param  string $name
     * @return binary
     */
    protected function compileRepository($path, $name, $subname)
    {
        $stub = $this->files->get($this->getStubPath('repository.stub'));
        $stub = $this->stubs->make($stub, $name, $subname)
                            ->replaceAll()
                            ->get();

        $this->files->put($path . "/Contracts/Repositories/" . $name . "Repository.php", $stub);
    }

    /**
     * Create the Eloquent repository file.
     *
     * @param  string $path
     * @param  string $name
     * @return binary
     */
    protected function compileRepositoryEloquent($path, $name, $subname)
    {
        $stub = $this->files->get($this->getStubPath('repository_eloquent.stub'));
        $stub = $this->stubs->make($stub, $name, $subname)
                            ->replaceAll()
                            ->get();

        $this->files->put($path . "/Repositories/" . $name . "EloquentRepository.php", $stub);
    }

    /**
     * Create the controller file.
     *
     * @param  string $path
     * @param  string $name
     * @return binary
     */
    protected function compileController($path, $name, $subname)
    {
        $stub = $this->files->get($this->getStubPath('controller.stub'));
        $stub = $this->stubs->make($stub, $name, $subname)
                            ->replaceAll()
                            ->get();

        $this->files->put($path . "/Controllers/" . $name . "Controller.php", $stub);
    }

    /**
     * Create the test file.
     *
     * @param  string $path
     * @param  string $name
     * @return binary
     */
    protected function compileTest($path, $name, $subname)
    {
        $stub = $this->files->get($this->getStubPath('unit_test.stub'));
        $stub = $this->stubs->make($stub, $name, $subname)
                            ->replaceAll()
                            ->get();

        $this->files->put(base_path("tests/" . $name . "Test.php"), $stub);
    }

    /**
     * Create the routes file.
     *
     * @param  string $path
     * @return binary
     */
    protected function compileRoutes($path, $name, $subname)
    {
        $stub = $this->files->get($this->getStubPath('routes.stub'));
        $stub = $this->stubs->make($stub, $name, $subname)
                            ->replaceAll()
                            ->get();

        $this->files->put($path . "/routes.php", $stub);
    }

    /**
     * Create the routes file.
     *
     * @param  string $path
     * @return binary
     */
    protected function compileApiRoutes($path, $name, $subname)
    {
        $stub = $this->files->get($this->getStubPath('routes_api.stub'));
        $stub = $this->stubs->make($stub, $name, $subname)
                            ->replaceAll()
                            ->get();

        $this->files->put($path . "/apiroutes.php", $stub);
    }

    protected function compileModuleJson($path, $name, $subname)
    {
        $stub = $this->files->get($this->getStubPath('module_json.stub'));
        $stub = $this->stubs->make($stub, $name, $subname)
                            ->replaceAll()
                            ->get();

        $this->files->put($path . "/module.json", $stub);

    }
}