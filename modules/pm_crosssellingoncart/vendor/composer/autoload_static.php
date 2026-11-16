<?php
namespace Composer\Autoload;
class ComposerStaticInit644d94af0710a123bdb727103209c968
{
    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );
    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit644d94af0710a123bdb727103209c968::$classMap;
        }, null, ClassLoader::class);
    }
}
