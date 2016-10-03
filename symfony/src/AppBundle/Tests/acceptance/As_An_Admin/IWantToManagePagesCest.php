<?php

namespace As_An_Admin;

use AcceptanceTester;
use Common;

class IWantToManagePagesCest
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
        $I->click('Page Management');
    }

    /**
     * Test page list action.
     *
     * scenario 19.11
     *
     * @before login
     */
    public function listPages(AcceptanceTester $I)
    {
        // there should be 3 parent menus
        $I->canSeeNumberOfElements('//div[@id="nestable"]/ol/li', 3);
        // there should be 2 entries under the about menu
        $I->click('expand all');
        $I->canSeeNumberOfElements('//li[@data-id="2"]/ol/li', 2);
    }

    /**
     * Test page show action.
     *
     * scenario 19.12
     *
     * @before login
     */
    public function showContactUsPage(AcceptanceTester $I)
    {
        $I->click('contact_us');
        // i should see "en: Contact_us"
        $I->canSee('en: Contact');
    }

    /**
     * Test ajax submission by reordering menu.
     *
     * scenario 19.13
     *
     * @before login
     */
    public function reorderHomePage(AcceptanceTester $I)
    {
        $I->click('expand all');
        $I->dragAndDrop('//li[@data-id="4"]/div', '//li[@data-id="1"]/div');
        $I->waitForText('menu has been reordered successfully');
        // we should now have 4 main li
        $I->canSeeNumberOfElements('//div[@id="nestable"]/ol/li', 4);
        // refresh page and reorder it back to original state
        $I->click('Page Management');
        $I->click('expand all');
        $I->dragAndDrop('//li[@data-id="4"]/div', '//li[@data-id="3"]/div');
        $I->waitForText('menu has been reordered successfully');
        $I->canSeeNumberOfElements('//div[@id="nestable"]/ol/li', 3);
    }

    /**
     * Test page edit action.
     *
     * scenario 19.14
     *
     * @before login
     */
    public function editHomePage(AcceptanceTester $I)
    {
        $I->click('home');
        $I->click('Edit');
        $I->fillField('//input[@name="page[slug]"]', 'home1');
        // update
        $I->click('Save changes');
        // back at page management page, i should see home1
        $I->click('Page Management');
        $I->canSee('home1');
        $I->click('home1');
        $I->click('Edit');
        $I->fillField('//input[@name="page[slug]"]', 'home');
        // update
        $I->click('Save changes');
    }

    /**
     * Test new and delete action for both page and pagemeta.
     *
     * scenario 19.15
     *
     * @before login
     */
    public function createDeleteTestPage(AcceptanceTester $I)
    {
        // add new page
        $I->click('new page');
        $I->fillField('//input[@name="page[slug]"]', 'test_page');
        $I->fillField('//input[@name="page[sequence]"]', '1');
        $I->selectOption('#page_parent', 'about');
        $I->click('//input[@name="page[isPublished]"]');
        $I->click('Save changes');
        // add new page meta
        $I->click('Page Management');
        $I->click('new pagemeta');
        $I->fillField('//input[@name="pagemeta[page_title]"]', 'test page title');
        $I->fillField('//input[@name="pagemeta[menu_title]"]', 'test menu title');
        $I->selectOption('#pagemeta_page', 'test_page');
        $I->click('Save changes');
        // now back to list page. we check that the page contains meta
        $I->click('expand all');
        $I->click('test_page');
        $I->canSee('en: test menu title');
        $I->click('Delete');
        $I->waitForElementVisible('#modal-delete-button');
        $I->click('#modal-delete-button');
        // now back to list page.
        $I->click('expand all');
        $I->cantSee('test_page');
    }

    /**
     * If we remove a page, the associated pagemeta should be removed as well.
     *
     * scenario 19.16
     *
     * @before login
     */
    public function deleteContactUsPage(AcceptanceTester $I)
    {
        $I->click('contact_us');
        $I->click('Delete');
        $I->waitForElementVisible('#modal-delete-button');
        $I->click('#modal-delete-button');

        // we now connect to do and make sure the page and pagemetas are updated.
        $I->seeNumRecords(0, 'page', ['id' => 5]);
        $I->seeNumRecords(0, 'pagemeta', ['page_id' => 5]);
    }

    /**
     * check that there should only be 1 locale of each type per pagemeta.
     *
     * scenario 19.17
     *
     * @before login
     */
    public function singleLocalePerPageMeta(AcceptanceTester $I)
    {
        $I->click('new pagemeta');
        $I->fillField('//input[@name="pagemeta[page_title]"]', 'test page title');
        $I->fillField('//input[@name="pagemeta[menu_title]"]', 'test menu title');
        $I->click('Save changes');
        $I->canSee('Sorry, there is already a pagemeta with this locale.');
    }
}
