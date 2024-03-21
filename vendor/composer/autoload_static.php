<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit83dc51f18499c5b442343696b7634cfe
{
    public static $prefixLengthsPsr4 = array (
        'B' => 
        array (
            'Brunofreire\\SunatDolarLaravel\\' => 30,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Brunofreire\\SunatDolarLaravel\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit83dc51f18499c5b442343696b7634cfe::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit83dc51f18499c5b442343696b7634cfe::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit83dc51f18499c5b442343696b7634cfe::$classMap;

        }, null, ClassLoader::class);
    }
}
