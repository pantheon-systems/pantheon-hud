Feature: Pantheon HUD plugin

  Background:
    Given I log in as an admin

  Scenario: Plugin is loaded
    When I go to "/wp-admin/"
    Then the "#wp-admin-bar-pantheon-hud" element should contain "assets/img/pantheon-fist-color.svg"

  Scenario: Clear the site cache
    When I go to "/wp-admin/options-general.php?page=pantheon-cache"
    Then I should see "Clear Site Cache"
    And I should not see "Site cache flushed."

    When I press "Clear Cache"
    Then print current URL
    And I should be on "/wp-admin/options-general.php?page=pantheon-cache&cache-cleared=true"
    And I should see "Site cache flushed." in the ".notice, .updated" element
