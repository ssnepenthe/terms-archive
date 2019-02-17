<?php

use SsnTestKit\ManagesOptions;
use SsnTestKit\ManagesRewrites;

class RewritesTest extends TaTestCase
{
    use ManagesOptions,
        ManagesRewrites;

    /** @test */
    public function it_registers_rewrites_for_all_taxonomies_supported_by_current_theme()
    {
        $this->activateTheme('ta-twentyfifteen-child');
        $this->activatePlugin('terms-archive');

        // Plugin activation deletes rewrites - we need to make sure they are regenerated.
        $this->flushRewrites();

        $rewrites = $this->getOption('rewrite_rules');

        $this->assertArrayHasKey('category/?$', $rewrites);
        $this->assertArrayHasKey('category/page/([0-9]{1,})/?$', $rewrites);
        $this->assertArrayHasKey('tag/?$', $rewrites);
        $this->assertArrayHasKey('tag/page/([0-9]{1,})/?$', $rewrites);

        // Post formats - eligible but not supported by theme.
        $this->assertArrayNotHasKey('type/?$', $rewrites);
        $this->assertArrayNotHasKey('type/page/([0-9]{1,})/?$', $rewrites);
    }

    /** @test */
    public function it_does_not_register_rewrites_for_taxonomies_disabled_via_settings()
    {
        $this->activateTheme('ta-twentyfifteen-child');
        $this->activatePlugin('terms-archive');

        // Plugin activation deletes rewrites - we need to regenerate them.
        $this->flushRewrites();

        $rewrites = $this->getOption('rewrite_rules');

        $this->assertArrayHasKey('category/?$', $rewrites);
        $this->assertArrayHasKey('category/page/([0-9]{1,})/?$', $rewrites);

        $this->setOption('ta_settings', [
            'disabled' => ['category'],
            'version' => '0.1.0',
        ]);

        $this->flushRewrites();

        $rewrites = $this->getOption('rewrite_rules');

        $this->assertArrayNotHasKey('category/?$', $rewrites);
        $this->assertArrayNotHasKey('category/page/([0-9]{1,})/?$', $rewrites);
    }

    /** @test */
    public function it_does_not_register_rewrites_for_supported_non_public_taxonomies()
    {
        $this->activateTheme('ta-twentyfifteen-child');
        $this->activatePlugin('ta-custom-taxonomies');
        $this->activatePlugin('terms-archive');

        // Plugin activation deletes rewrites - we need to regenerate them.
        $this->flushRewrites();

        $rewrites = $this->getOption('rewrite_rules');

        $this->assertArrayNotHasKey('ta_snp/?$', $rewrites);
        $this->assertArrayNotHasKey('ta_snp/page/([0-9]{1,})/?$', $rewrites);
    }

    /** @test */
    public function it_does_not_register_rewrites_for_supported_non_publicly_queryable_taxonomies()
    {
        $this->activateTheme('ta-twentyfifteen-child');
        $this->activatePlugin('ta-custom-taxonomies');
        $this->activatePlugin('terms-archive');

        // Plugin activation deletes rewrites - we need to regenerate them.
        $this->flushRewrites();

        $rewrites = $this->getOption('rewrite_rules');

        $this->assertArrayNotHasKey('ta_snpq/?$', $rewrites);
        $this->assertArrayNotHasKey('ta_snpq/page/([0-9]{1,})/?$', $rewrites);
    }

    /** @test */
    public function it_does_not_register_rewrites_for_supported_taxonomies_with_rewrite_disabled()
    {
        $this->activateTheme('ta-twentyfifteen-child');
        $this->activatePlugin('ta-custom-taxonomies');
        $this->activatePlugin('terms-archive');

        // Plugin activation deletes rewrites - we need to regenerate them.
        $this->flushRewrites();

        $rewrites = $this->getOption('rewrite_rules');

        $this->assertArrayNotHasKey('ta_srd/?$', $rewrites);
        $this->assertArrayNotHasKey('ta_srd/page/([0-9]{1,})/?$', $rewrites);
    }

    /** @test */
    public function it_includes_rewrite_front_when_registering_rewrites()
    {
        $this->activateTheme('ta-twentyfifteen-child');
        $this->activatePlugin('terms-archive');

        $this->flushRewrites();

        $rewrites = $this->getOption('rewrite_rules');


        $this->assertArrayHasKey('category/?$', $rewrites);
        $this->assertArrayNotHasKey('blog/category/?$', $rewrites);

        $this->setPermalinkStructure('/blog/%postname%/');

        $rewrites = $this->getOption('rewrite_rules');

        $this->assertArrayHasKey('blog/category/?$', $rewrites);
        $this->assertArrayNotHasKey('category/?$', $rewrites);
    }
}
