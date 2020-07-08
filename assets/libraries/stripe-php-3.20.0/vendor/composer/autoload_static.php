<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf1f714bb81d3656c6432a2898dc2a3d9
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Stripe\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Stripe\\' => 
        array (
            0 => __DIR__ . '/..' . '/stripe/stripe-php/lib',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf1f714bb81d3656c6432a2898dc2a3d9::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf1f714bb81d3656c6432a2898dc2a3d9::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
