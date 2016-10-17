<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit20460887ce4b46658a6b626961005106
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'Abraham\\TwitterOAuth\\' => 21,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Abraham\\TwitterOAuth\\' => 
        array (
            0 => __DIR__ . '/..' . '/abraham/twitteroauth/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit20460887ce4b46658a6b626961005106::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit20460887ce4b46658a6b626961005106::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
