<?php

// autoload_static.php @generated by Composer

namespace Wordlift_Modules_Include_Exclude_Push_Config_Composer\Autoload;

class ComposerStaticInit3d5ab0830c7bf52ba5addfd65f37fd54
{
    public static $classMap = array (
        'ComposerAutoloaderInit3d5ab0830c7bf52ba5addfd65f37fd54' => __DIR__ . '/..' . '/composer/autoload_real.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Wordlift\\Modules\\Include_Exclude_Push_Config\\Include_Exclude_API' => __DIR__ . '/../..' . '/Include_Exclude_API.php',
        'Wordlift\\Modules\\Include_Exclude_Push_Config\\Include_Exclude_Default_Config_Installer' => __DIR__ . '/../..' . '/Include_Exclude_Default_Config_Installer.php',
        'Wordlift_Modules_Include_Exclude_Push_Config_Composer\\Autoload\\ClassLoader' => __DIR__ . '/..' . '/composer/ClassLoader.php',
        'Wordlift_Modules_Include_Exclude_Push_Config_Composer\\Autoload\\ComposerStaticInit3d5ab0830c7bf52ba5addfd65f37fd54' => __DIR__ . '/..' . '/composer/autoload_static.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit3d5ab0830c7bf52ba5addfd65f37fd54::$classMap;

        }, null, ClassLoader::class);
    }
}
