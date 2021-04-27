<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit01953eedced6fd6809c6397407b5e463
{
    public static $prefixLengthsPsr4 = array (
        'J' => 
        array (
            'Josantonius\\LanguageCode\\' => 25,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Josantonius\\LanguageCode\\' => 
        array (
            0 => __DIR__ . '/..' . '/josantonius/languagecode/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit01953eedced6fd6809c6397407b5e463::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit01953eedced6fd6809c6397407b5e463::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit01953eedced6fd6809c6397407b5e463::$classMap;

        }, null, ClassLoader::class);
    }
}
