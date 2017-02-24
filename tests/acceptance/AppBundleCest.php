<?php


class AppBundleCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    // tests
    public function InstallationTest(AcceptanceTester $I)
    {
        $I->wantTo('Check if Symfony is installed');
        $I->amOnPage('http://songbird.app/');
        $I->see('Symfony 3.1.4');
    }
}
