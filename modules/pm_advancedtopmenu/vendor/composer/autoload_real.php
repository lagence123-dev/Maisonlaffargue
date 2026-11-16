<?php
class ComposerAutoloaderInit46d38c98ee19fc4d699ed3bbca2b125a
{
    private static $loader;
    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }
        spl_autoload_register(array('ComposerAutoloaderInit46d38c98ee19fc4d699ed3bbca2b125a', 'loadClassLoader'), true, false);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit46d38c98ee19fc4d699ed3bbca2b125a', 'loadClassLoader'));
        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit46d38c98ee19fc4d699ed3bbca2b125a::getInitializer($loader));
        $loader->register(false);
        return $loader;
    }
}
