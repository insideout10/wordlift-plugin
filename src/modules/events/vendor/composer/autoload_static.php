<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitca2d76f00d9dc05bb61631fe434b572d
{
    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Wordlift\\Modules\\Events\\Options_Entity\\Events_Options_Entity_Include_Exclude' => __DIR__ . '/../..' . '/includes/Options_Entity/Events_Options_Entity_Include_Exclude.php',
        'Wordlift\\Modules\\Events\\Post_Entity\\Events_Post_Entity_Jsonld' => __DIR__ . '/../..' . '/includes/Post_Entity/Events_Post_Entity_Jsonld.php',
        'Wordlift\\Modules\\Events\\Term_Entity\\Events_Term_Entity_Jsonld' => __DIR__ . '/../..' . '/includes/Term_Entity/Events_Term_Entity_Jsonld.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInitca2d76f00d9dc05bb61631fe434b572d::$classMap;

        }, null, ClassLoader::class);
    }
}
