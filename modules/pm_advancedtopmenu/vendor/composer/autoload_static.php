<?php
namespace Composer\Autoload;
class ComposerStaticInit46d38c98ee19fc4d699ed3bbca2b125a
{
    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );
    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit46d38c98ee19fc4d699ed3bbca2b125a::$classMap;
        }, null, ClassLoader::class);
    }
}
