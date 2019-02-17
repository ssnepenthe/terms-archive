<?php

class ViewsTest extends TaTestCase
{
    /** @test */
    public function it_adds_plugin_specific_classes_to_the_body_class()
    {
        $this->activateTheme('ta-twentyfifteen-child');
        $this->activatePlugin('terms-archive');

        $bodyClasses = array_flip(
            explode(' ', $this->browser()->get('/category/')->filter('body')->attr('class'))
        );

        $this->assertArrayHasKey('ta-terms-archive', $bodyClasses);
        $this->assertArrayHasKey('ta-terms-archive-category', $bodyClasses);

        $this->markTestIncomplete(
            '@todo "Empty" taxonomy archives are currently broken due to #12'
            . ' (https://github.com/ssnepenthe/terms-archive/issues/12)'
        );

        $bodyClasses = array_flip(
            explode(' ', $this->browser()->get('/tag/')->filter('body')->attr('class'))
        );

        $this->assertArrayHasKey('ta-terms-archive', $bodyClasses);
        $this->assertArrayHasKey('ta-terms-archive-post_tag', $bodyClasses);
    }

    /** @test */
    public function it_filters_archive_description_to_output_taxonomy_description()
    {
        $this->activateTheme('ta-twentyfifteen-child');
        $this->activatePlugin('terms-archive');

        $this->browser()
            ->get('/category/')
            ->assertSeeText('Some awesome category description!');

        $this->markTestIncomplete(
            '@todo "Empty" taxonomy archives are currently broken due to #12'
            . ' (https://github.com/ssnepenthe/terms-archive/issues/12)'
        );

        $this->browser()
            ->get('/tag/')
            ->assertSeeText('And a cool description for tags!');
    }

    /** @test */
    public function it_uses_default_archive_description_when_taxonomy_cant_be_found()
    {
        // @todo Maybe make it so this happens when description is not set?
        // @todo Maybe merge with the previous test?
    }

    /** @test */
    public function it_filters_archive_title_to_output_taxonomy_label()
    {
        // @todo Test escaping?
        $this->activateTheme('ta-twentyfifteen-child');
        $this->activatePlugin('terms-archive');

        $this->assertEquals(
            'Categories',
            $this->browser()->get('/category/')->filter('.page-title')->text()
        );

        $this->markTestIncomplete(
            '@todo "Empty" taxonomy archives are currently broken due to #12'
            . ' (https://github.com/ssnepenthe/terms-archive/issues/12)'
        );

        $this->assertEquals(
            'Tags',
            $this->browser()->get('/tag/')->filter('.page-title')->text()
        );
    }

    /** @test */
    public function it_uses_default_archive_title_when_taxonomy_cant_be_found()
    {
        // @todo Maybe make this happen also when label is not set?
        // @todo Maybe merge with previous test?
    }

    /** @test */
    public function it_filters_document_title_to_output_taxonomy_label()
    {
        // @todo Test escaping?
        $this->activateTheme('ta-twentyfifteen-child');
        $this->activatePlugin('terms-archive');

        $this->assertStringStartsWith(
            'Categories',
            $this->browser()->get('/category/')->filter('title')->text()
        );

        $this->markTestIncomplete(
            '@todo "Empty" taxonomy archives are currently broken due to #12'
            . ' (https://github.com/ssnepenthe/terms-archive/issues/12)'
        );

        $this->assertStringStartsWith(
            'Tags',
            $this->browser()->get('/tag/')->filter('title')->text()
        );
    }

    /** @test */
    public function it_uses_default_document_title_when_taxonomy_cant_be_found()
    {
        // @todo Maybe make this happen also when label is not set?
        // @todo Maybe merge with previous test?
    }

    /** @test */
    public function it_correctly_includes_the_expected_template_for_terms_archives()
    {
        // @todo Test both ta-terms-archive.php and ta-terms-archive-{$taxonomy}.php
    }
}
