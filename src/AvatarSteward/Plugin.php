<?php

declare(strict_types=1);

namespace AvatarSteward;

final class Plugin
{
    private static ?self $instance = null;

    private function __construct()
    {
        if (function_exists('add_action')) {
            add_action('plugins_loaded', [$this, 'boot']);
        }
    }

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function boot(): void
    {
        if (function_exists('do_action')) {
            do_action('avatarsteward/booted');
        }
    }
}
