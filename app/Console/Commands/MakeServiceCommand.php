<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

/**
 * Create a new service class.
 */
class MakeServiceCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * Ex:
     * php artisan make:service DepositService
     */
    protected $name = 'make:service';

    /**
     * The console command description.
     */
    protected $description = 'Create a new service class';

    /**
     * The type of class being generated.
     */
    protected $type = 'Service';

    /**
     * Get the stub file for the generator.
     */
    protected function getStub()
    {
        return base_path('stubs/service.stub');
    }

    /**
     * Get the default namespace for the class.
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Services';
    }
}
