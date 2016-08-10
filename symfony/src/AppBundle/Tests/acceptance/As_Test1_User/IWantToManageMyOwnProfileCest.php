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
        $I->click('test1');
        $I->canSee('test1@songbird.app');
        $I->canSee('Email');
        $I->waitForElement('//img[contains(@src, "test_profile")]');
    }

    /**
     * Scenario 10.4.2
     * @before login
     */
    public function hidUneditableFields(AcceptanceTester $I)
    {
        $I->click('test1');
        $I->click('Edit');
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
        $I->click('test1');
        $I->click('Edit');
        $I->fillField('//input[@value="test1 Lastname"]', 'lastname1 updated');
        // submit form
        $I->click('//button[@type="submit"]');
        // i am on the show page
        $I->canSeeInCurrentUrl('/admin/?action=show&entity=User&id=2');
        $I->canSee('lastname1 updated');
        // now revert changes
        $I->click('test1');
        $I->click('Edit');
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

        $I->click('test1');
        $I->click('Edit');
        $I->fillField('//input[contains(@id, "_plainPassword_first")]', '123');
        $I->fillField('//input[contains(@id, "_plainPassword_second")]', '123');

        // update
        $I->click('//button[@type="submit"]');

        // // I should be able to login with the new password
        $I->amOnPage('/logout');
        Common::login($I, TEST1_USERNAME, '123');
        // i can login and at dashboard now
        $I->canSee('Dear test1');

        // reset everything back
        $I->amOnPage('/admin/?action=edit&entity=User&id=2');
        $I->fillField('//input[contains(@id, "_plainPassword_first")]', TEST1_PASSWORD);
        $I->fillField('//input[contains(@id, "_plainPassword_second")]', TEST1_PASSWORD);
        $I->click('//button[@type="submit"]');
        // i am on the show page
        $I->canSeeInCurrentUrl('/admin/?action=show&entity=User&id=2');
        // i should be able to login with the old password
        $this->login($I);
        $I->canSee('Dear test1');
    }

    /**
     * Scenario 10.4.5
     * @before login
     */
    public function deleteAndAddProfileImage(AcceptanceTester $I)
    {
        // get original image
        $imagePath = $I->grabFromDatabase('user', 'image', array('username' => 'test1'));
        // check image available
        $I->canSeeFileFound($imagePath, '../../web/uploads/profiles');

        $I->click('test1');
        $I->click('Edit');
        $I->click('//input[@id="user_imageFile_delete"]');
        // submit form
        $I->click('//button[@type="submit"]');
        // i am on the show page
        $I->canSeeInCurrentUrl('/admin/?action=show&entity=User&id=2');
        // can see empty images
        $I->canSee('Empty');
        // check that image is not there
        $I->cantSeeFileFound($imagePath, '../../web/uploads/profiles');

        // now revert changes
        $I->click('test1');
        $I->click('Edit');
        $I->waitForElementVisible('//input[@type="file"]');
        $I->attachFile('//input[@type="file"]', 'test_profile.jpg');
        // update
        $I->click('//button[@type="submit"]');
        // get image from db
        $imagePath = $I->grabFromDatabase('user', 'image', array('username' => 'test1'));
        // check image available
        $I->canSeeFileFound($imagePath, '../../web/uploads/profiles');
    }

    /**
     * Scenario 10.4.6
     * @before login
     */
    public function updateProfileImageOnly(AcceptanceTester $I)
    {
        // get original image
        $imagePath = $I->grabFromDatabase('user', 'image', array('username' => 'test1'));
        // check image available
        $I->canSeeFileFound($imagePath, '../../web/uploads/profiles');

        $I->click('test1');
        $I->click('Edit');
        $I->attachFile('//input[@type="file"]', 'test_profile.jpg');
        // submit form
        $I->click('//button[@type="submit"]');

        // get new id
        $imagePath = $I->grabFromDatabase('user', 'image', array('username' => 'test1'));
        // i am on the show page
        $I->canSeeInCurrentUrl('/admin/?action=show&entity=User&id=2');
        // can see new image
        $I->waitForElement('//img[contains(@src, "'.$imagePath.'")]');
        $I->canSeeFileFound($imagePath, '../../web/uploads/profiles');
    }
}
