<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit3d5ab0830c7bf52ba5addfd65f37fd54
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Wordlift_Modules_Include_Exclude_Push_Config_Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Wordlift_Modules_Include_Exclude_Push_Config_Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInit3d5ab0830c7bf52ba5addfd65f37fd54', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Wordlift_Modules_Include_Exclude_Push_Config_Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit3d5ab0830c7bf52ba5addfd65f37fd54', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Wordlift_Modules_Include_Exclude_Push_Config_Composer\Autoload\ComposerStaticInit3d5ab0830c7bf52ba5addfd65f37fd54::getInitializer($loader));

        $loader->setClassMapAuthoritative(true);
        $loader->register(true);

        return $loader;
    }
}
