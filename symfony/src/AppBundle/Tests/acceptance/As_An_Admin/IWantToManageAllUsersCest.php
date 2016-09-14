<?php
namespace As_An_Admin;
use \AcceptanceTester;
use \Common;

class IWantToManageAllUsersCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    protected function login(AcceptanceTester $I)
    {
        Common::login($I, ADMIN_USERNAME, ADMIN_PASSWORD);
    }

    /**
     * Scenario 10.6.1
     * @before login
     */
    public function listAllProfiles(AcceptanceTester $I)
    {
    	$I->click('User Management');
        $I->canSeeNumberOfElements('//table/tbody/tr',4);
        $I->seeNumberOfElements('//td[@data-label="Image"]',4);
    }

    /**
     * Scenario 10.6.2
     * @before login
     */
    public function showTest3User(AcceptanceTester $I)
    {
        // go to user listing page
        $I->click('User Management');
        // click on show button
        $I->click('Show');
        $I->waitForText('test3@songbird.app');
        $I->canSee('test3@songbird.app');
    }

    /**
     * Scenario 10.6.3
     * @before login
     */
    public function editTest3User(AcceptanceTester $I)
    {
        // go to user listing page
        $I->click('User Management');
        // click on edit button
        $I->click('Edit');
        // check we are on the right url
        $I->canSeeInCurrentUrl('/admin/?action=edit&entity=User');
        $I->fillField('//input[@value="test3 Lastname"]', 'lastname3 updated');
        // update
        $I->click('//button[@type="submit"]');
        // go back to listing page
        $I->amOnPage('/admin/?action=list&entity=User');
        $I->canSee('lastname3 updated');
        // now revert username
        $I->amOnPage('/admin/?action=edit&entity=User&id=4');
        $I->fillField('//input[@value="lastname3 updated"]', 'test3 Lastname');
        $I->click('//button[@type="submit"]');
        $I->amOnPage('/admin/?action=list&entity=User');
        $I->canSee('test3 Lastname');
    }

    /**
     * Scenario 10.6.4
     * @before login
     */
    public function createAndDeleteNewUser(AcceptanceTester $I)
    {
        // go to create page and fill in form
        $I->click('User Management');
        $I->click('Add User');
        $I->fillField('//input[contains(@id, "_username")]', 'test4');
        $I->fillField('//input[contains(@id, "_email")]', 'test4@songbird.app');
        $I->fillField('//input[contains(@id, "_plainPassword_first")]', 'test4');
        $I->fillField('//input[contains(@id, "_plainPassword_second")]', 'test4');
        // submit form
        $I->click('//button[@type="submit"]');
        // go back to user list
        $I->amOnPage('/admin/?entity=User&action=list');
        // i should see new test4 user created
        $I->canSee('test4@songbird.app');

        // now delete user
        // click on edit button
        $I->click('Delete');
        // wait for model box and then click on delete button
        $I->waitForElementVisible('//button[@id="modal-delete-button"]');
        $I->click('//button[@id="modal-delete-button"]');
        // I can no longer see test4 user
        $I->cantSee('test4@songbird.app');
    }
}
