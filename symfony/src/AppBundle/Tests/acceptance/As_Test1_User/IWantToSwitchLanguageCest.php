<?php
namespace As_Test1_User;
use \AcceptanceTester;
use \Common;

class IWantToSwitchLanguageCest
{
    public function _before(AcceptanceTester $I)
    {
        Common::login($I, TEST1_USERNAME, TEST1_PASSWORD);
    }

    public function _after(AcceptanceTester $I)
    {
    }

    /**
     * Scenario 13.1.1
     */
    public function localeInFrench(AcceptanceTester $I)
    {
        // switch to french
        $I->selectOption('//select[@id="lang"]', 'fr');
        // I should be able to see "my profile" in french
        $I->canSee('Déconnexion');
        $I->click('test1');
        // now in show profile page
        $I->canSee("Éditer");
        // now switch back to english
        $I->selectOption('//select[@id="lang"]', 'en');
        $I->canSee('Edit');
    }
}
