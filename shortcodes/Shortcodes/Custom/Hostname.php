<?php

namespace Shortcodes\Custom;

use Maiorano\Shortcodes\Library\SimpleShortcode;

class Hostname
{
    public function __invoke()
    {
        return new SimpleShortcode(
            'hostname',
            null,
            function($content = null, array $atts = []) {
                return gethostname();
            }
        );
    }
}
