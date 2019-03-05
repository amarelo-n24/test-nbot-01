<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit4c68721c3e6797158a1429d3ae274cb0
{
    public static $prefixLengthsPsr4 = array (
        'L' => 
        array (
            'LINE\\' => 5,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'LINE\\' => 
        array (
            0 => __DIR__ . '/..' . '/linecorp/line-bot-sdk/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit4c68721c3e6797158a1429d3ae274cb0::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit4c68721c3e6797158a1429d3ae274cb0::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}