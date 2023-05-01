<?php

declare(strict_types=1);

use Drupal\TestTools\PhpUnitCompatibility\ClassWriter;
use Drupal\TestTools\PhpUnitCompatibility\PhpUnit8\ClassWriter as ClassWriterD9;

$loader = require __DIR__ . '/../vendor/autoload.php';

// Start with classes in known locations.
$loader->add('Drupal\\BuildTests', __DIR__ . '/../vendor/drupal/core/tests');
$loader->add('Drupal\\Tests', __DIR__ . '/../vendor/drupal/core/tests');
$loader->add('Drupal\\TestSite', __DIR__ . '/../vendor/drupal/core/tests');
$loader->add('Drupal\\KernelTests', __DIR__ . '/../vendor/drupal/core/tests');
$loader->add('Drupal\\FunctionalTests', __DIR__ . '/../vendor/drupal/core/tests');
$loader->add('Drupal\\FunctionalJavascriptTests', __DIR__ . '/../vendor/drupal/core/tests');
$loader->add('Drupal\\TestTools', __DIR__ . '/../vendor/drupal/core/tests');
$loader->addPsr4('Drupal\\sqlite\\', __DIR__ . '/../vendor/drupal/core/modules/sqlite/src');
$loader->addPsr4('Drupal\\Tests\\user\\', __DIR__ . '/../vendor/drupal/core/modules/user/tests/src');

if (class_exists(ClassWriter::class)) {
    ClassWriter::mutateTestBase($loader);
} elseif (class_exists(ClassWriterD9::class)) {
    ClassWriterD9::mutateTestBase($loader);
}

file_put_contents(__DIR__ . '/../vendor/drupal/autoload.php', <<<AUTOLOAD
<?php

/**
 * @file
 * Includes the autoloader created by Composer.
 *
 * This file was generated by drupal-scaffold.
 *
 * @see composer.json
 * @see index.php
 * @see core/install.php
 * @see core/rebuild.php
 * @see core/modules/statistics/statistics.php
 */

return require __DIR__ . '/../autoload.php';

AUTOLOAD);
