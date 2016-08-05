<?php
namespace As_Test1_User;
use \AcceptanceTester;
use \Common;


class IWantToManageMyOwnProfileCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    protected function login(AcceptanceTester $I)
    {
        Common::login($I, TEST1_USERNAME, TEST1_PASSWORD);
    }

    /**
     * Scenario 10.4.1
     * @before login
     */
    public function showMyProfile(AcceptanceTester $I)
    {
        $I->amOnPage('/admin/?action=show&entity=User&id=2');
        $I->canSee('test1@songbird.app');
        $I->canSee('Email');
    }

    /**
     * Scenario 10.4.2
     * @before login
     */
    public function hidUneditableFields(AcceptanceTester $I)
    {
        $I->amOnPage('/admin/?action=edit&entity=User&id=2');
        $I->cantSee('Enabled');
        $I->cantSee('Locked');
        $I->cantSee('Roles');
    }

    /**
     * Scenario 10.4.3
     * @before login
     */
    public function updateFirstnameOnly(AcceptanceTester $I)
    {
        $I->amOnPage('/admin/?action=edit&entity=User&id=2');
        $I->fillField('//input[@value="test1 Lastname"]', 'lastname1 updated');
        // submit form
        $I->click('//button[@type="submit"]');
        // i am on the show page
        $I->canSeeInCurrentUrl('/admin/?action=show&entity=User&id=2');
        $I->canSee('lastname1 updated');
        // now revert changes
        $I->amOnPage('/admin/?action=edit&entity=User&id=2');
        $I->fillField('//input[@value="lastname1 updated"]', 'test1 Lastname');
        // update
        $I->click('//button[@type="submit"]');
    }

    /**
     * Scenario 10.4.4
     * @before login
     */
    public function updatePasswordOnly(AcceptanceTester $I)
    {

        $I->amOnPage('/admin/?action=edit&entity=User&id=2');
        $I->fillField('//input[contains(@id, "_plainPassword_first")]', '123');
        $I->fillField('//input[contains(@id, "_plainPassword_second")]', '123');

        // update
        $I->click('//button[@type="submit"]');

        // // I should be able to login with the new password
        $I->amOnPage('/logout');
        Common::login($I, TEST1_USERNAME, '123');
        // at dashboard now
        $I->canSee('Invalid credentials.');

        // reset everything back
        $I->amOnPage('/admin/?action=edit&entity=User&id=2');
        $I->fillField('//input[contains(@id, "_plainPassword_first")]', TEST1_PASSWORD);
        $I->fillField('//input[contains(@id, "_plainPassword_second")]', TEST1_PASSWORD);
        $I->click('//button[@type="submit"]');
        // i am on the show page
        $I->canSeeInCurrentUrl('/admin/?action=show&entity=User&id=2');
        // i should be able to login with the old password
        $this->login($I);
        $I->canSee('Access denied.');
    }
}
