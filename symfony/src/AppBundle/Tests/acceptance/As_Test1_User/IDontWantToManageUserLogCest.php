<?php
namespace As_Test1_User;
use \AcceptanceTester;
use \Common;

class IDontWantToManageUserLogCest
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
     * Scenario 15.2.1
     * @before login
     */
    public function listUserLogs(AcceptanceTester $I)
    {
        $I->cantSee('User Log');
        $I->amOnPage('/admin/?entity=UserLog&action=list');
	    $I->canSee('access denied');
    }

    /**
     * Scenario 15.2.2
     * @before login
     */
    public function showFirstEntry(AcceptanceTester $I)
    {
        // go to user listing page
        $I->amOnPage('/admin/?entity=UserLog&action=show&id=1');
        $I->canSee('access denied');
    }

    /**
     * Scenario 15.2.3
     * @before login
     */
    public function editFirstEntry(AcceptanceTester $I)
    {
        // go to user listing page
        $I->amOnPage('/admin/?entity=UserLog&action=edit&id=1');
        $I->canSee('access denied');
    }
}
