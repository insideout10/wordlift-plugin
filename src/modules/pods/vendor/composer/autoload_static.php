<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1fa7477671b8ce4e96024c7b54cda86e
{
    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'Wordlift\\Modules\\Pods\\' => 22,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Wordlift\\Modules\\Pods\\' => 
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
            $loader->prefixLengthsPsr4 = ComposerStaticInit1fa7477671b8ce4e96024c7b54cda86e::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit1fa7477671b8ce4e96024c7b54cda86e::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit1fa7477671b8ce4e96024c7b54cda86e::$classMap;

        }, null, ClassLoader::class);
    }
}
