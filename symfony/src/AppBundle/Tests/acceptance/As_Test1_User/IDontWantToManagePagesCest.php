<?php
namespace As_Test1_User;

use \AcceptanceTester;
use \Common;

class IDontWantToManagePagesCest
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
	 * Scenario 19.21
	 * @before login
	 */
	public function listPages(AcceptanceTester $I)
	{
		$I->amOnPage('/admin/?entity=Page&action=list');
		$I->canSee('Access Denied');
	}
	/**
	 * Scenario 19.22
	 * @before login
	 */
	public function showAboutUsPage(AcceptanceTester $I)
	{
		$I->amOnPage('/admin/?entity=Page&action=show&id=2');
		$I->canSee('Access Denied');
	}
	/**
	 * Scenario 19.23
	 * @before login
	 */
	public function editAboutUsPage(AcceptanceTester $I) {
		$I->amOnPage('/admin/?entity=Page&action=edit&id=2');
		$I->canSee('Access Denied');
	}
}
