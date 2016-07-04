Feature: Shortcodes
  As a developer
  In order to add runtime values to examples
  I need to use shortcode tokens to represent runtime values

  Scenario: Shortcode in a step string
    Given the date shortcode is defined
    When I check the value in the string "[date type=now format=Y-m-d]"
    Then I see it is todays date in year-month-day format

  Scenario: Undefined shortcode ignored in a step string
    Given the date shortcode is defined
    When I check the value in the string "[undefined]"
    Then I see it is the word undefined in square brackets

  Scenario: Shortcode in a table
    Given the date shortcode is defined
    When I check the value in:
      |[date type=now format=Y-m-d]|[date type=now format=Y-m-d]|
      |[date type=now format=Y-m-d]|[date type=now format=Y-m-d]|

    Then I see it is todays date in year-month-day format

  Scenario: Shortcode in a pystring
    Given the date shortcode is defined
    When I check the string value in:
      """
      [date type=now format=Y-m-d]
      """

    Then I see it is todays date in year-month-day format

  Scenario: Shortcode is not cached
    Given the date shortcode is defined
    And I wait for two seconds
    When I check the value in the string "[date type=now format=H:i:s]"
    Then I see it is the time of day now
