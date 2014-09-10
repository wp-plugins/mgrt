<?php

// Ensure that composer has installed all dependencies
if (!file_exists(dirname(__DIR__).'/composer.lock')) {
    die("Dependencies must be installed using composer:\n\nphp composer.phar install\n\n"
        . "See http://getcomposer.org for help with installing composer\n");
}

// Include the composer autoloader
$loader = require dirname(__DIR__).'/vendor/autoload.php';
$loader->add('Mgrt\\\Test', __DIR__);

// Register services with the GuzzleTestCase
Guzzle\Tests\GuzzleTestCase::setMockBasePath(__DIR__.'/mock');

// Check credentials
if (!isset($_SERVER['PUBLIC_KEY']) || !isset($_SERVER['PRIVATE_KEY'])) {
    die("Unable to get your public_key or private_key \n");
}

// Instantiate the service builder
$api = \Mgrt\Client::factory(array(
    'public_key'  => $_SERVER['PUBLIC_KEY'],
    'private_key' => $_SERVER['PRIVATE_KEY'],
));

// Configure the tests to ise the instantiated service builder
$serviceBuilder = new Guzzle\Service\Builder\ServiceBuilder();
$serviceBuilder->set('mgrt', $api);
Guzzle\Tests\GuzzleTestCase::setServiceBuilder($serviceBuilder);

// Emit deprecation warnings
Guzzle\Common\Version::$emitWarnings = true;
