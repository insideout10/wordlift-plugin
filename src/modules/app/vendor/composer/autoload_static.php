<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitbbd4d8293152407d0a77bfbb28311dea
{
    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'Wordlift\\Modules\\App\\' => 21,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Wordlift\\Modules\\App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitbbd4d8293152407d0a77bfbb28311dea::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitbbd4d8293152407d0a77bfbb28311dea::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitbbd4d8293152407d0a77bfbb28311dea::$classMap;

        }, null, ClassLoader::class);
    }
}
