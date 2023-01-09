@mod @mod_wordcloud

Feature: Separate group mode in a wordcloud

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And the following "groups" exist:
      | name | course | idnumber |
      | Group A | C1 | G1 |
      | Group B | C1 | G2 |
    And the following "group members" exist:
      | user | group |
      | student1 | G1 |
      | student2 | G1 |
      | student2 | G2 |
    And the following "activities" exist:
      | activity   | name                   | intro                         | course | idnumber     | groupmode |
      | wordcloud  | Test wordcloud         | Test wordcloud description    | C1     | groups       | 1         |
    And I log out
    And I log in as "student2"
    And I am on the "Test wordcloud" "wordcloud activity" page
    And I select "Group A" from the "Separate groups" singleselect
    And I set the field "mod-wordcloud-new-word" to "word1GroupA"
    And I press "mod-wordcloud-btn"
    And I select "Group B" from the "Separate groups" singleselect
    And I set the field "mod-wordcloud-new-word" to "word1GroupB"
    And I press "mod-wordcloud-btn"
    And I log out

  @javascript
  Scenario: Teacher with accessallgroups can view all groups
    Given I log in as "teacher1"
    When I am on the "Test wordcloud" "wordcloud activity" page
    Then the "Separate groups" select box should contain "All participants"
    And the "Separate groups" select box should contain "Group A"
    And the "Separate groups" select box should contain "Group B"
    And I select "All participants" from the "Separate groups" singleselect
    And I should see "word1GroupA"
    And I should see "word1GroupB"
    And I select "Group A" from the "Separate groups" singleselect
    And I should see "word1GroupA"
    And I should not see "word1GroupB"
    And I select "Group B" from the "Separate groups" singleselect
    And I should see "word1GroupB"
    And I should not see "word1GroupA"

  @javascript
  Scenario: Students can only see and submit words in their group
    Given I log in as "student1"
    And I am on the "Test wordcloud" "wordcloud activity" page
    Then I should see "word1GroupA"
    And I should see "Submit"
    And I should not see "word1GroupB"
    And "Separate groups" "select" should not exist
    And I log out
    When I log in as "student2"
    And I am on the "Test wordcloud" "wordcloud activity" page
    Then the "Separate groups" select box should not contain "All participants"
    When I select "Group A" from the "Separate groups" singleselect
    Then I should see "word1GroupA"
    And I should see "Submit"
    When I select "Group B" from the "Separate groups" singleselect
    Then I should see "word1GroupB"
    And I should see "Submit"
