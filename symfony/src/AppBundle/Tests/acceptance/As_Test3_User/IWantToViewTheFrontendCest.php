<?php
namespace As_Test3_User;
use \AcceptanceTester;

class IWantToViewTheFrontendCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

	/**
	 * scenario 20.11
	 *
	 * @param AcceptanceTester $I
	 */
    public function homepageIsWorking(AcceptanceTester $I)
    {
	    $I->amOnPage('/');
	    $I->canSeeElement('.jumbotron');
	    $I->canSee('SongBird CMS Demo');
    }

	/**
	 * scenario 20.12
	 * @param AcceptanceTester $I
	 */
	public function menusAreWorking(AcceptanceTester $I)
	{
		$I->amOnPage('/');
		// should be able to use the movemouseover method but its not working on bootstrap
		$I->canSeeNumberOfElements('//ul[@id="top_menu"]/li', 6);
	}

	/**
	 * Scenario 20.13
	 *
	 * @param AcceptanceTester $I
	 */
	public function subPagesAreWorking(AcceptanceTester $I)
	{
		$I->amOnPage('/');
		$I->click('Contact');
		$I->canSee('This project is hosted in');
	}

	/**
	 * Scenario 20.14
	 *
	 * @param AcceptanceTester $I
	 */
	public function loginMenuWorking(AcceptanceTester $I)
	{
		$I->amOnPage('/');
		$I->click('Log in');
		$I->canSeeNumberOfElements('//ul[@id="top_menu"]/li', 3);
	}
}
