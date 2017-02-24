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
    /*
     * check homepage is active
     */
     /*
    public function InstallationTest(AcceptanceTester $I)
    {
        $I->wantTo('Check if Symfony is installed');
        $I->amOnPage('http://songbird.app/');
        $I->see('Symfony 3.1.4');
    }
    */
    /**
     * check homepage is not active
     *
     * @param AcceptanceTester $I
     */
    public function RemovalTest(AcceptanceTester $I)
    {
        $I->wantTo('Check if homepage is not active');
        $I->amOnPage('http://songbird.app/');
        $I->see('404 Not Found');
    }
}
