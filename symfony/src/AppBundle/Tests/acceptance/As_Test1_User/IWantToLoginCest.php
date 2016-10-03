<?php

namespace As_Test1_User;

use AcceptanceTester;
use Common;

class IWantToLoginCest
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
     * Scenario 10.1.1.
     */
    public function wrongLoginCredentials(AcceptanceTester $I)
    {
        Common::login($I, TEST1_USERNAME, '123');
        $I->canSee('Invalid credentials');
    }

    /**
     * Scenario 10.1.2.
     *
     * @before login
     */
    public function seeMyDashboardContent(AcceptanceTester $I)
    {
        $I->canSee('Dear test1');
        $I->canSeeNumberOfElements('//ul[@class="sidebar-menu"]/li', 2);
    }

    /**
     * Scenario 10.1.3.
     *
     * @before login
     */
    public function logoutSuccessfully(AcceptanceTester $I)
    {
        $I->amOnPage('/logout');
        // now user should be redirected to home page
        $I->canSeeInCurrentUrl('/');
    }

    /**
     * Scenario 10.1.4.
     */
    public function AccessAdminWithoutLoggingIn(AcceptanceTester $I)
    {
        $I->amOnPage('/admin/dashboard');
        // now user should be redirected to login page
        $I->canSeeInCurrentUrl('/login');
    }
}
