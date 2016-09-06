<?php
/**
 * Registers a simple autoload closure. Require this file,
 * if onesheet was _not_ installed via composer.
 */
spl_autoload_register(function ($class) {
    // skip right away, if its not OneSheet
    if (0 !== strpos($class, 'OneSheet')) {
        return;
    }

    // define absolute src path
    $srcPath = dirname(__FILE__) . '/src/';

    // replace namespace backslash with slash & append tp $srcPath
    $filePath = $srcPath . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

    // require class file, if it exists
    if (is_readable($filePath)) {
        require $filePath;
    }
});
