<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    const UNDEFINED_SHORTCODE = '[undefined]';

    private $value;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @Given the date shortcode is defined
     */
    public function theDateShortcodeIsDefined()
    {
    }

    /**
     * @Given the environment shortcode is defined
     */
    public function theEnvironmentShortcodeIsDefined()
    {
    }

    /**
     * @Given I wait for two seconds
     */
    public function iWaitForTwoSeconds()
    {
        sleep(2);
    }

    /**
     * @When I check the value in the string :arg1
     */
    public function iCheckTheValueInTheString($string)
    {
        $this->value = $string;
    }

    /**
     * @When I check the value in:
     */
    public function iCheckTheValueIn(TableNode $table)
    {
        $this->value = $table->getRow(0)[0];
    }

    /**
     * @When I check the string value in:
     */
    public function iCheckTheStringValueIn(PyStringNode $string)
    {
        $this->value = $string->getRaw();
    }

    /**
     * @Then I see it is todays date in year-month-day format
     */
    public function iSeeItIsTodaysDateInYearMonthDayFormat()
    {
        $date = new \DateTime('now', new \DateTimeZone('UTC'));
        $expected = $date->format('Y-m-d');

        if ($expected != $this->value) {
            throw new \Exception(sprintf('Value mismatch - found: %s, expected: %s', $this->value, $expected));
        }
    }

    /**
     * @Then I see it is the word undefined in square brackets
     */
    public function iSeeItIsTheWordUndefinedInSquareBrackets()
    {
        if (self::UNDEFINED_SHORTCODE != $this->value) {
            throw new \Exception(sprintf('Value mismatch - found: %s, expected: %s', $this->value, self::UNDEFINED_SHORTCODE));
        }
    }

    /**
     * @Then I see it is the time of day now
     */
    public function iSeeItIsTheTimeOfDayNow()
    {
        $date = new \DateTime('now', new \DateTimeZone('UTC'));
        $expected = $date->format('H:i:s');

        if ($expected != $this->value) {
            throw new \Exception(sprintf('Value mismatch - found: %s, expected: %s', $this->value, $expected));
        }
    }

    /**
     * @Then I see it is the current environment user
     */
    public function iSeeItIsTheCurrentEnvironmentUser()
    {
        $expected = getenv('USER');

        if ($expected != $this->value) {
            throw new \Exception(sprintf('Value mismatch - found: %s, expected: %s', $this->value, $expected));
        }
    }

    /**
     * @Then I see it is the current hostname
     */
    public function iSeeItIsTheCurrentHostname()
    {
        $expected = gethostname();

        if ($expected != $this->value) {
            throw new \Exception(sprintf('Value mismatch - found: %s, expected: %s', $this->value, $expected));
        }
    }
}
