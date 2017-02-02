<?php

namespace Netinteractive\Elegant\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Netinteractive\Elegant\Utils\FieldGenerator;

/**
 * Class MakeElegant
 * @package Netinteractive\Elegant\Console\Commands
 */
class MakeElegant extends Command
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * @var FieldGenerator
     */
    protected $fieldGenerator;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:elegant';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create elegant model';

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var string
     */
    protected $modelDir;

    /**
     * @var string
     */
    protected $domainDir;


    /**
     * Create a new migration creator instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
        $this->fieldGenerator  = \App::make('elegant.make.fieldGenerator');
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $table = $this->ask('Table name');
        $name = ucfirst(strtolower($this->ask('Model name')));

        $this->modelDir = $this->ask('Model dir', 'Model');
        $this->domainDir = $this->ask('Domain dir', 'Domain');

        // prepare name
        $modelNamespace = $this->buildModelNamespace($name);
        $domainNamespace = $this->buildDomainNamespace($name);


        //get field list
        $this->grabFields($table);

        // get stubs
        $repoStub = $this->getRepositoryStub($modelNamespace);

        $recordStub = $this->getRecordStub($modelNamespace);
        $blueprintStub = $this->getBlueprintStub($modelNamespace, $table);
        $serviceStub = $this->getServiceStub($modelNamespace, $domainNamespace);

        $scopeStub = $this->getScopeStub($modelNamespace);

        // get paths
        $repoPath = $this->getPath($modelNamespace, 'Repository');
        $recordPath = $this->getPath($modelNamespace, 'Record');
        $bpPath = $this->getPath($modelNamespace, 'Blueprint');
        $scopePath = $this->getPath($modelNamespace, 'Scope');
        $servicePath = $this->getPath($domainNamespace, $name.'ServiceProvider');

        // create files
        $this->make($repoPath, $repoStub);
        $this->make($recordPath, $recordStub);
        $this->make($bpPath, $blueprintStub);
        $this->make($scopePath, $scopeStub);
        $this->make($servicePath, $serviceStub);


        $this->comment('Model generated successfully!');
        $this->comment('To make Repository::search work please bind record:');

        $parts = explode('\\',$name );
        $modelName = $parts[count($parts)-1];
        $this->comment("\$app->bind('$modelName', '$modelNamespace\Record');");
    }

    /**
     * @param $table
     */
    protected function grabFields($table)
    {
        $driverName = \DB::connection()->getDriverName();
        $generator =  $this->fieldGenerator->get($driverName);

        $this->fields = $generator->getFieldsList($table);
    }

    /**
     * @param $path
     * @param $stub
     */
    protected function make($path, $stub)
    {
        $this->makeDirectory($path);
        $this->files->put($path, $stub);
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @param  string $file
     * @return string
     */
    protected function getPath($name, $file)
    {
        $name = str_replace($this->laravel->getNamespace(), '', $name);

        return $this->laravel['path'].'/'.ucwords($name).'/'.str_replace('\\', '/', $file).'.php';
    }


    /**
     * Build the directory for the class if necessary.
     *
     * @param  string  $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (! $this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
    }

    /**
     * Build model namespace according to the root namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildModelNamespace($name)
    {
        $rootNamespace = $this->laravel->getNamespace();

        $name = $this->modelDir.'\\'.$name;

        $name = str_replace('\\', ' ', $name);
        $name = ucwords($name);
        $name = str_replace(' ', '\\', $name);

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        if (Str::contains($name, '/')) {
            $name = str_replace('/', '\\', $name);
        }

        return $this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\'.$name;
    }

    /**
     * Build domain namespace according to the root namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildDomainNamespace($name)
    {
        $rootNamespace = $this->laravel->getNamespace();

        $name = $this->domainDir.'\\'.$name;
        $name = str_replace('\\', ' ', $name);
        $name = ucwords($name);
        $name = str_replace(' ', '\\', $name);

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        if (Str::contains($name, '/')) {
            $name = str_replace('/', '\\', $name);
        }

        return $this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\'.$name;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace;
    }

    /**
     * @param string $nameSpace
     * @param string $table
     * @return string
     */
    protected function getBlueprintStub($nameSpace, $table)
    {
        $stub = $this->files->get($this->getStubPath()."/blueprint.stub");
        $stub = str_replace('{Namespace}', $nameSpace, $stub);
        $stub = str_replace('{TableName}', $table, $stub);

        $fieldsStr  = var_export($this->fields, true);
        $stub = str_replace('{Fields}', $fieldsStr, $stub);


        return $stub;
    }

    /**
     * @param string $nameSpace
     * @return mixed
     */
    protected function getScopeStub($nameSpace)
    {
        $stub = $this->files->get($this->getStubPath()."/scope.stub");
        $stub = str_replace('{Namespace}', $nameSpace, $stub);

        $methods = '';
        foreach($this->fields as $field=>$data){
            $methodStub = $this->files->get($this->getStubPath()."/scope/method.stub");
            $methodStub = str_replace('{ucField}', Str::studly($field), $methodStub);
            $methodStub = str_replace('{field}',$field, $methodStub);

            $methods .= "\n";
            $methods .= $methodStub;
            $methods .= "\n";
        }

        $stub = str_replace('{Methods}', $methods, $stub);

        return $stub;
    }


    /**
     * @param string $nameSpace
     * @return string
     */
    protected function getRecordStub($nameSpace)
    {
        $stub = $this->files->get($this->getStubPath()."/record.stub");
        $stub = str_replace('{Namespace}', $nameSpace, $stub);

        $methods = '';
        foreach($this->fields as $field=>$data){
            $methodStub = $this->files->get($this->getStubPath()."/record/method.stub");
            $methodStub = str_replace('{ucField}', Str::studly($field), $methodStub);
            $methodStub = str_replace('{field}',$field, $methodStub);

            $methods .= "\n";
            $methods .= $methodStub;
            $methods .= "\n";
        }

        $stub = str_replace('{Methods}', $methods, $stub);

        return $stub;
    }

    /**
     * @param string $nameSpace
     * @return string
     */
    protected function getRepositoryStub($nameSpace)
    {
        $stub = $this->files->get($this->getStubPath()."/repository.stub");
        $stub = str_replace('{Namespace}', $nameSpace, $stub);

        return $stub;
    }

    /**
     * @param string $modelNamespace
     * @param string $domainNamespace
     * @return string
     */
    public function getServiceStub($modelNamespace, $domainNamespace)
    {
        $stub = $this->files->get($this->getStubPath()."/service.stub");
        $stub = str_replace('{Namespace}', $domainNamespace, $stub);
        $stub = str_replace('{ModelNamespace}', $modelNamespace, $stub);

        return $stub;
    }

    /**
     * Get the filesystem instance.
     *
     * @return \Illuminate\Filesystem\Filesystem
     */
    public function getFilesystem()
    {
        return $this->files;
    }

    /**
     * Get the path to the stubs.
     *
     * @return string
     */
    public function getStubPath()
    {
        return __DIR__.'/stubs';
    }

}
