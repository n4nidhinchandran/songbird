<?php
namespace As_An_Admin;

use \AcceptanceTester;
use \Common;

class IWantToManageUserLogCest
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
     * Scenario 15.1.1
     * @before login
     */
    public function listUserLogs(AcceptanceTester $I)
    {
        $I->click('User Log');
        $tr = $I->grabMultiple('//tr');
        $I->assertGreaterThan(1, count($tr));
    }

    /**
     * Scenario 15.1.2
     * @before login
     */
    public function showFirstEntry(AcceptanceTester $I)
    {
        // go to user listing page
        $I->amOnPage('/admin/?entity=UserLog&action=show&id=1');
        $I->canSee('/admin/dashboard');
    }
    
}
