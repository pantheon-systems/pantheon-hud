Feature: Solr Power plugin

  Background:
    Given I log in as an admin

  Scenario: Plugin is loaded
    When I go to "/wp-admin/"
    Then the "#wp-admin-bar-pantheon-hud" element should contain "pantheon-hud.pantheonsite.io"
    And the "#wp-admin-bar-pantheon-hud" element should contain "1 app container running PHP"
    And the "#wp-admin-bar-pantheon-hud" element should contain "1 db container with replication disabled"
    And the "#wp-admin-bar-pantheon-hud" element should contain "Visit Pantheon Dashboard"
