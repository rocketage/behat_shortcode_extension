Feature: Custom shortcodes
  As a developer
  In order to add runtime values to examples for my own domain
  I need to define custom shortcodes to use in my features

  Scenario: Custom shell user shortcode in a step string
    Given the environment shortcode is defined
    When I check the value in the string "[shelluser]"
    Then I see it is the current environment user

  Scenario: Custom hostname shortcode in a step string
    Given the environment shortcode is defined
    When I check the value in the string "[hostname]"
    Then I see it is the current hostname
