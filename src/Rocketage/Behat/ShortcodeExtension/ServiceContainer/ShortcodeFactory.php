<?php

namespace Rocketage\Behat\ShortcodeExtension\ServiceContainer;

use Maiorano\Shortcodes\Manager\ManagerInterface as ShortcodeProcessor;

class ShortcodeFactory
{
    private $processor;
    private $shortcodes;

    public function __construct(ShortcodeProcessor $processor, array $shortcodes)
    {
        $this->processor = $processor;
        $this->shortcodes = $shortcodes;
    }

    public function getProcessor()
    {
        foreach ($this->shortcodes as $shortcode) {
            $this->processor->register($shortcode());
        }
        return $this->processor;
    }
}
