<?php

namespace Shortcodes\Custom;

use Maiorano\Shortcodes\Library\SimpleShortcode;

class ShellUser
{
    public function __invoke()
    {
        return new SimpleShortcode(
            'shelluser',
            null,
            function($content = null, array $atts = []) {
                return getenv('USER');
            }
        );
    }
}
