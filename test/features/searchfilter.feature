Feature: Test itop
  In order access the application, users must be able to log in

  @pro @com
  Scenario: config voit toutes les organisations
    Given there is an iTop installed with the standard datamodel and sample data 'searchfilter'
    And I login as "config/config"
    And I have a valid user account 'config/config'
    And I wait for 2 seconds
    Then I should see "Welcome"
	#Then I should see "Type your keywords to filter below menus"
   # And I fill in "Type your keywords to filter below menus" with "configuration"
  #  And I wait for 2 seconds
   # Then I follow "Déconnexion"
