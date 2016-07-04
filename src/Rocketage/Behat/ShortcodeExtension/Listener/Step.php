<?php

namespace Rocketage\Behat\ShortcodeExtension\Listener;

use Behat\Behat\EventDispatcher\Event\BeforeStepTested;
use Behat\Behat\EventDispatcher\Event\StepTested;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Maiorano\Shortcodes\Manager\ManagerInterface as ShortcodeProcessor;
use Behat\Gherkin\Node\StepNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Gherkin\Node\PyStringNode;


class Step implements EventSubscriberInterface
{
    /**
     * @var ShortcodeProcessor
     */
    private $processor;

    /**
     * Step constructor.
     * @param ShortcodeProcessor $processor
     */
    public function __construct(ShortcodeProcessor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            StepTested::BEFORE => ['beforeStep', 10]
        );
    }

    /**
     * @param BeforeStepTested $event
     */
    public function beforeStep(BeforeStepTested $event)
    {
        $step = $event->getStep();

        $this->processStepText($step);
        $this->processStepArguments($step);
    }

    /**
     * @param StepNode $step
     */
    private function processStepText(StepNode $step)
    {
        $reflectedStep = new \ReflectionObject($step);
        $textProperty = $reflectedStep->getProperty('text');
        $textProperty->setAccessible(true);

        $textProperty->setValue($step, $this->processor->doShortcode($textProperty->getValue($step)));
    }

    /**
     * @param StepNode $step
     */
    private function processStepArguments(StepNode $step)
    {
        if (!$step->hasArguments()) {
            return;
        }

        $newArguments = [];
        foreach ($step->getArguments() as $argument) {
            if ($argument instanceof TableNode) {
                $argument = $this->processTableNode($argument);
            } else if ($argument instanceof PyStringNode) {
                $argument = $this->processPyStringNode($argument);
            }

            $newArguments[] = $argument;
        }

        $reflectedStep = new \ReflectionObject($step);
        $argumentProperty = $reflectedStep->getProperty('arguments');
        $argumentProperty->setAccessible(true);

        $argumentProperty->setValue($step, $newArguments);
    }

    /**
     * @param TableNode $table
     * @return TableNode
     */
    private function processTableNode(TableNode $table)
    {
        $processor = $this->processor;

        $newTable = array_map(
            function($row) use ($processor) {
                return array_map(
                    function($value) use ($processor) {
                        return $processor->doShortcode($value);
                    },
                    $row
                );
            },
            $table->getRows()
        );

        return new TableNode($newTable);
    }

    /**
     * @param PyStringNode $string
     * @return PyStringNode
     */
    private function processPyStringNode(PyStringNode $string)
    {
        $processor = $this->processor;

        return new PyStringNode(
            array_map(
                function($line) use ($processor) {
                    return $processor->doShortcode($line);
                },
                $string->getStrings()
            ),
            $string->getLine()
        );
    }
}
