<?php

use SsnTestKit\ManagesOptions;
use SsnTestKit\ManagesUserSessions;

class OptionsPageTest extends TaTestCase
{
    use ManagesOptions,
        ManagesUserSessions;

    /** @test */
    public function it_gracefully_handles_case_where_current_theme_does_not_support_terms_archive()
    {
        $this->activateTheme('twentyfifteen');
        $this->activatePlugin('terms-archive');
        $this->login();

        $this->browser()
            ->get('/wp-admin/options-general.php?page=terms-archive')
            ->assertSeeText('Your theme doesn\'t appear to support terms archives.');
    }

    /** @test */
    public function it_lists_all_supported_taxonomies_as_checkboxes()
    {
        $this->activateTheme('ta-twentyfifteen-child');
        $this->activatePlugin('terms-archive');
        $this->login();

        $response = $this->browser()
            ->get('/wp-admin/options-general.php?page=terms-archive')
            ->assertSeeTextInOrder(['category', 'post_tag'])
            ->assertDontSeeText('post_format');

        $this->assertCount(2, $response->filter('input[type="checkbox"]'));
    }

    /** @test */
    public function it_correctly_checks_boxes_for_taxonomies_that_are_disabled()
    {
        $this->activateTheme('ta-twentyfifteen-child');
        $this->activatePlugin('terms-archive');
        $this->login();

        // $this->disableArchivesFor('category');

        $this->setOption('ta_settings', [
            'disabled' => ['category'],
            'version' => '0.1.0',
        ]);

        $response = $this->browser()->get('/wp-admin/options-general.php?page=terms-archive');

        // Category should be checked.
        $this->assertEquals('checked', $response->filter('#ta_settings_category')->attr('checked'));

        // Post tags should not.
        $this->assertNull($response->filter('#ta_settings_post_tag')->attr('checked'));
    }

    /** @test */
    public function it_strips_invalid_taxonomies_from_disabled_list_on_save()
    {
        $this->activateTheme('ta-twentyfifteen-child');
        $this->activatePlugin('terms-archive');
        $this->login();

        $values = $this->browser()
            ->get('/wp-admin/options-general.php?page=terms-archive')
            ->crawler()
            ->selectButton('Save Changes')
            ->form()
            ->getPhpValues();

        $values['ta_settings']['disabled'] = ['post_tag', 'category', 'notreal'];

        // Skip the BrowserKit/DomCrawler form APIs so I don't need to think about validation.
        $this->browser()->post('/wp-admin/options.php', $values);

        // Gross. This should probably be tested via the admin UI instead, not really sure how...
        $options = $this->getOption('ta_settings');

        $this->assertEquals(['category', 'post_tag'], $options['disabled']);
    }

    /** @test */
    public function it_flushes_rewrites_after_updating_disabled_taxonomies()
    {
        $this->activateTheme('ta-twentyfifteen-child');
        $this->activatePlugin('terms-archive');
        $this->login();

        $this->browser()->get('/category/')->assertOk();

        $values = $this->browser()
            ->get('/wp-admin/options-general.php?page=terms-archive')
            ->crawler()
            ->selectButton('Save Changes')
            ->form()
            ->getPhpValues();

        $values['ta_settings']['disabled'] = ['category'];

        // Skip the BrowserKit/DomCrawler form APIs so I don't need to think about validation.
        $this->browser()->post('/wp-admin/options.php', $values);

        $this->browser()->get('/category/')->assertNotFound();
    }
}
