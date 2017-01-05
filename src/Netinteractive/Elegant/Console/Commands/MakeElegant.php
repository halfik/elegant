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
        $name = $this->ask('Model name');
        $name = ucfirst(strtolower($name));

        $name = $this->parseName($name);

        $recordStub = $this->getRecordStub($name);
        $blueprintStub = $this->getBlueprintStub($name, $table);


        $recordPath = $this->getPath($name, 'Record');
        $bpPath = $this->getPath($name, 'Blueprint');

        $this->makeDirectory($recordPath);
        $this->makeDirectory($bpPath);

        $this->files->put($recordPath, $recordStub);
        $this->files->put($bpPath, $blueprintStub);

        //$this->files->put($path, $this->populateStub($name, $stub, $table));

        //$this->firePostCreateHooks();

        //return $path;

        $this->comment('Model generated successfully!');
    }


    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name, $file)
    {
        $name = str_replace('\\', ' ', $name);
        $name = ucwords($name);
        $name = str_replace(' ', '\\', $name);
        $name = str_replace($this->laravel->getNamespace(), '', $name);
        echo $name; exit;
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
     * Parse the name and format according to the root namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function parseName($name)
    {
        $rootNamespace = $this->laravel->getNamespace();

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        if (Str::contains($name, '/')) {
            $name = str_replace('/', '\\', $name);
        }

        return $this->parseName($this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\'.$name);
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
     * @param string $name
     * @param string $table
     * @return string
     */
    protected function getBlueprintStub($name, $table)
    {
        $stub = $this->files->get($this->getStubPath()."/blueprint.stub");
        $stub = str_replace('{Namespace}', $name, $stub);
        $stub = str_replace('{TableName}', $table, $stub);

        $driverName = \DB::connection()->getDriverName();
        $generator =  $this->fieldGenerator->get($driverName);

        $fields = $generator->getFieldsList($table);
        $fieldsStr = $results = var_export($fields, true);
        $stub = str_replace('{Fields}', $fieldsStr, $stub);


        return $stub;
    }


    /**
     * @param string $name
     * @return string
     */
    protected function getRecordStub($name)
    {
        $stub = $this->files->get($this->getStubPath()."/record.stub");
        $stub = str_replace('{Namespace}', $name, $stub);

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
