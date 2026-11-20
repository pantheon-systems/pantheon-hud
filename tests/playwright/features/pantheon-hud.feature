Feature: Pantheon HUD plugin

  Background:
    Given I log in as an admin

  Scenario: Plugin is loaded
    And I visit "/wp-admin/"
    Then the "#wp-admin-bar-pantheon-hud" element should contain the HTML "assets/img/pantheon-fist-color.svg"
