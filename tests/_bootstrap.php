<?php
// This is global bootstrap for autoloading


require '../../../bootstrap/autoload.php';

$app = require_once '../../../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Run The Artisan Application
|--------------------------------------------------------------------------
|
| When we run the console application, the current CLI command will be
| executed in this console and the response sent back to a terminal
| or another output device for the developers. Here goes nothing!
|
*/

$kernel = $app->make('Illuminate\Contracts\Console\Kernel');

$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\ArgvInput,
    new Symfony\Component\Console\Output\ConsoleOutput
);




require 'unit/ElegantTest.php';