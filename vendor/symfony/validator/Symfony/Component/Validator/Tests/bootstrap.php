<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

spl_autoload_register(function ($class) {
    if (0 === strpos(ltrim($class, '/'), 'Symfony\Component\Validator')) {
        if (file_exists($file = __DIR__.'/../'.substr(str_replace('\\', '/', $class), strlen('Symfony\Component\Validator')).'.php')) {
            require_once $file;
        }
    }
});

if (file_exists($loader = __DIR__.'/../vendor/autoload.php')) {
    require_once $loader;
}

if (class_exists($annotationRegistry = 'Doctrine\Common\Annotations\AnnotationRegistry')) {
    $annotationRegistry::registerLoader(function($class) {
        if (0 === strpos(ltrim($class, '/'), 'Symfony\Component\Validator')) {
            if (file_exists($file = __DIR__.'/../'.substr(str_replace('\\', '/', $class), strlen('Symfony\Component\Validator')).'.php')) {
                require_once $file;
            }
        }

        return class_exists($class, false);
    });
}