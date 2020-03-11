Feature: Solr Power plugin

  Background:
    Given I log in as an admin

  Scenario: Plugin is loaded
    When I go to "/wp-admin/"
    Then the "#wp-admin-bar-pantheon-hud" element should contain "assets/img/pantheon-fist-color.svg"
