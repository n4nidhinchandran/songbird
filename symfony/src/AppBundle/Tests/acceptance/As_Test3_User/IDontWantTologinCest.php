<?php

namespace As_Test3_User;

use AcceptanceTester;
use Common;

class IDontWantTologinCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    /**
     * Scenario 10.3.1.
     */
    public function AccountDisabled(AcceptanceTester $I)
    {
        Common::login($I, TEST3_USERNAME, TEST3_PASSWORD);
        // i cannot login and at dashboard now
        $I->canSee('account is disabled.');
    }
}
