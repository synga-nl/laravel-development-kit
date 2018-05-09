<?php

$baseDir = '/data/www/';
$testProject = 'test123';

$vendorSymlinks = [
    'vendor/synga/laravel-development-kit' => $baseDir . 'laravel-development-kit',
    'vendor/synga/interactive-console-tester' => $baseDir . 'interactive-console-tester'
];

$baseFullDir = $baseDir . $testProject;
$composerPath = $baseFullDir . '/composer.json';

if (file_exists($baseFullDir)) {
    exec('rm -rf ' . $baseFullDir);
}

exec('cd ' . $baseDir . ' && composer create-project --prefer-dist laravel/laravel ' . $testProject);

$composerFile = json_decode(file_get_contents($composerPath), true);
$composerFile['require']['synga/laravel-development-kit'] = 'dev-master';
$composerFile['require']['synga/interactive-console-tester'] = 'dev-master';

if (empty($composerFile['require'])) {
    unset($composerFile['require']);
}

file_put_contents(
    $composerPath,
    json_encode(
        $composerFile,
        JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
    )
);

exec('cd ' . $baseFullDir . ' && composer update');

foreach ($vendorSymlinks as $local => $remote) {
    exec('cd ' . $baseFullDir . ' && rm -rf ' . $local . ' && ln -s ' . $remote . ' ' . $local);
}

exec('cd ' . $baseFullDir . ' && php artisan vendor:publish --provider="Synga\LaravelDevelopment\LaravelDevelopmentServiceProvider"');
// fix this
exec('cd ' . $baseFullDir . ' && php artisan development:setup');
