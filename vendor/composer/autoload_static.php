<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit461d16c0bed9b07fb93841ed7c118dab
{
    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'Check\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Check\\' => 
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
            $loader->prefixLengthsPsr4 = ComposerStaticInit461d16c0bed9b07fb93841ed7c118dab::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit461d16c0bed9b07fb93841ed7c118dab::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit461d16c0bed9b07fb93841ed7c118dab::$classMap;

        }, null, ClassLoader::class);
    }
}
