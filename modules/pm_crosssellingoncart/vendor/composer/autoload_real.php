<?php
class ComposerAutoloaderInit644d94af0710a123bdb727103209c968
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
        spl_autoload_register(array('ComposerAutoloaderInit644d94af0710a123bdb727103209c968', 'loadClassLoader'), true, false);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit644d94af0710a123bdb727103209c968', 'loadClassLoader'));
        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit644d94af0710a123bdb727103209c968::getInitializer($loader));
        $loader->register(false);
        return $loader;
    }
}
