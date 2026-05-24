<?php

header('Content-Type: text/plain');

try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    
    // Boot console kernel
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    
    echo "Running optimize:clear...\n";
    $status = $kernel->call('optimize:clear');
    echo "Exit Status: $status\n";
    
    // Get output of the command
    $output = \Illuminate\Support\Facades\Artisan::output();
    echo "Output:\n" . $output . "\n";
} catch (Exception $e) {
    echo "Exception caught:\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
exit(0);
