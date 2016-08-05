<?php
namespace As_An_Admin;
use \AcceptanceTester;
use \Common;

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
        Common::login($I, ADMIN_USERNAME, ADMIN_PASSWORD);
    }

    /**
     * Scenario 10.2.1
     */
    public function wrongLoginCredentials(AcceptanceTester $I) {
        Common::login($I, ADMIN_USERNAME, '123');
        $I->canSee('Invalid credentials');
    }

    /**
     * Scenario 10.2.2
     * @before login
     */
    public function seeMyDashboardContent(AcceptanceTester $I) {
        $I->canSeeInCurrentUrl('/admin/?action=list&entity=User');
        $I->canSee('User Listing');
    }

    /**
     * Scenario 10.2.3
     * @before login
     */
    public function logoutSuccessfully(AcceptanceTester $I) {
        $I->amOnPage('/logout');
        // now user should be redirected to home page and it should be access denied for now.
        $I->canSeeInCurrentUrl('/');
    }

    /**
     * Scenario 10.2.4
     */
    public function AccessAdminWithoutLoggingIn(AcceptanceTester $I) {
        $I->amOnPage('/admin/?action=list&entity=User');
        // now user should be redirected to login page
        $I->canSeeInCurrentUrl('/login');
    }
}
