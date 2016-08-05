<?php
namespace As_test3_user;
use \AcceptanceTester;
use \Common;

class IDontWantTologinCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    protected function login(AcceptanceTester $I)
    {
        Common::login($I, TEST3_USERNAME, TEST3_PASSWORD);
    }

    /**
     * Scenario 10.3.1
     * @before login
     */
    public function AccountDisabled(AcceptanceTester $I) {
        $I->canSee('Account is disabled');
    }

}
