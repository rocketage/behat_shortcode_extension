<?php

namespace Rocketage\Behat\ShortcodeExtension\Shortcode;

use Maiorano\Shortcodes\Library\SimpleShortcode;

class Date
{
    public function __invoke()
    {
        return new SimpleShortcode(
            'date',
            null,
            function($content = null, array $atts = []) {
                $defaults = ['type' => 'now', 'format' => 'Y-m-d', 'zone' => 'UTC'];
                $params = array_merge($defaults, $atts);

                $date = new \DateTime($params['type'], new \DateTimeZone($params['zone']));

                return $date->format($params['format']);
            }
        );
    }
}
