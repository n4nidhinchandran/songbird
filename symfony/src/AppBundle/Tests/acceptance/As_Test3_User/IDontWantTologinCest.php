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

    /**
     * Scenario 10.3.1
     */
    public function AccountDisabled(AcceptanceTester $I) {
        Common::login($I, TEST3_USERNAME, TEST3_PASSWORD);
        // i can login and at dashboard now
        $I->canSee('Account is disabled.');
    }

}
