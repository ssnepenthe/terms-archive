<?php

use SsnTestKit\ManagesPosts;
use SsnTestKit\ManagesTerms;
use SsnTestKit\ManagesOptions;
use SsnTestKit\ManagesRewrites;

class EndpointsTest extends TaTestCase
{
    use ManagesOptions,
        ManagesPosts,
        ManagesRewrites,
        ManagesTerms;

    /** @test */
    public function it_creates_endpoints_for_all_taxonomies_supported_by_current_theme()
    {
        $this->activateTheme('ta-twentyfifteen-child');
        $this->activatePlugin('terms-archive');

        $post = $this->generatePosts();

        foreach ([
            // @todo Eventually terms per page will be overridable and this number will need to be adjusted.
            'category' => $this->generateCategories(11),
            'post_tag' => $this->generateTags(11),
            'post_format' => $this->generateTerms('post_format', 11),
        ] as $taxonomy => $ids) {
            foreach ($ids as $term) {
                // @todo String? Not a string?
                $this->addTermToPost((string) $post, $taxonomy, (string) $term);
            }
        }

        $this->browser()->get('/category/')->assertOk();
        $this->browser()->get('/category/page/2/')->assertOk();

        $this->browser()->get('/tag/')->assertOk();
        $this->browser()->get('/tag/page/2/')->assertOk();

        // @todo Look into post formats in general - they are disabled by the theme for this test
        // 		 but when they are enabled name stays blank and the archive page is basically empty.

        // Post formats - eligible but not supported by theme.
        $this->browser()->get('/type/')->assertNotFound();
        $this->browser()->get('/type/page/2/')->assertNotFound();
    }

    /** @test */
    public function it_does_not_create_endpoints_for_taxonomies_disabled_via_settings()
    {
        $this->activateTheme('ta-twentyfifteen-child');
        $this->activatePlugin('terms-archive');

        $this->browser()
            ->get('/category/')
            ->assertOk();

        $this->setOption('ta_settings', [
            'disabled' => ['category'],
            'version' => '0.1.0',
        ]);
        $this->cli()->wp('rewrite flush');

        $this->browser()
            ->get('/category/')
            ->assertNotFound();
    }

    /** @test */
    public function it_does_not_create_endpoints_for_supported_non_public_taxonomies()
    {
        $this->activateTheme('ta-twentyfifteen-child');
        $this->activatePlugin('ta-custom-taxonomies');
        $this->activatePlugin('terms-archive');

        $this->browser()
            ->get('/ta_snp/')
            ->assertNotFound();
    }

    /** @test */
    public function it_does_not_create_endpoints_for_supported_non_publicly_queryable_taxonomies()
    {
        $this->activateTheme('ta-twentyfifteen-child');
        $this->activatePlugin('ta-custom-taxonomies');
        $this->activatePlugin('terms-archive');

        $this->browser()
            ->get('/ta_snpq/')
            ->assertNotFound();
    }

    /** @test */
    public function it_does_not_create_endpoints_for_supported_taxonomies_with_rewrite_disabled()
    {
        $this->activateTheme('ta-twentyfifteen-child');
        $this->activatePlugin('ta-custom-taxonomies');
        $this->activatePlugin('terms-archive');

        $this->browser()
            ->get('/ta_srd/')
            ->assertNotFound();
    }

    /** @test */
    public function it_includes_rewrite_front_when_creating_endpoints()
    {
        $this->activateTheme('ta-twentyfifteen-child');
        $this->activatePlugin('terms-archive');

        $this->browser()->get('/category/')->assertOk();
        $this->browser()->get('/blog/category/')->assertNotFound();

        $this->setPermalinkStructure('/blog/%postname%/');

        $this->browser()->get('/category/')->assertNotFound();
        $this->browser()->get('/blog/category/')->assertOk();
    }

    // @todo current theme support
    // @todo wp query flags

    /** @test */
    public function it_returns_a_200_when_accessing_endpoint_for_taxonomy_with_no_posts()
    {
        $this->activateTheme('ta-twentyfifteen-child');
        $this->activatePlugin('terms-archive');

        // No posts attached...

        $response = $this->browser()->get('/tag/');

        $response->assertOk();

        $this->markTestIncomplete(
            'Plugin is not loading the correct template due to issue 12'
            . ' (https://github.com/ssnepenthe/terms-archive/issues/12)'
        );

        $response->assertSeeText('It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.');
    }

    // @todo Query short circuit.
}
