<?php

use SsnTestKit\ResetsSite;
use SsnTestKit\ManagesPosts;
use SsnTestKit\ManagesTerms;
use SsnTestKit\ManagesUserSessions;

class TemplateTagsTest extends TaTestCase
{
    use ManagesPosts,
        ManagesTerms,
        ManagesUserSessions,
        ResetsSite;

    /** @test */
    public function test_get_current_term()
    {
        $cat = $this->createCategory('A Category');
        $post = $this->createPost('A Post', 'Some post content.');

        $this->addCategoryToPost($post, $cat);

        $this->login();

        // The only (current) use of get_current_term() is in generating the term edit link.
        $editLinkSelector = ".ta-term-{$cat} .edit-link a";

        $this->assertEquals(
            sprintf(
                // @todo Check with WP core - Shouldn't this have been escaped?
                '%s/wp-admin/term.php?taxonomy=category&tag_ID=%s&post_type=post',
                static::browserBaseUri(),
                $cat
            ),
            $this->browser()->get('/category/')->filter($editLinkSelector)->attr('href')
        );
    }

    /** @test */
    public function test_get_loop()
    {
        // Not currently in use by theme.
    }

    /** @test */
    public function test_get_queried_taxonomy()
    {
        // Not currently in use by theme.
    }

    /** @test */
    public function test_get_term_class()
    {
        // Not in use directly but via the_term_class().
    }

    /** @test */
    public function test_get_term_content()
    {
        $cat = $this->createCategory('A Category', 'Just a category description.');
        $post = $this->createPost('A Post', 'Some post content.');

        $this->addCategoryToPost($post, $cat);

        $response = $this->browser()->get('/category/');

        $this->assertEquals(
            'Just a category description.',
            trim($response->filter("#term-{$cat} .entry-content")->text())
        );
    }

    /** @test */
    public function test_get_term_count()
    {
        $cat = $this->createCategory('A Category');
        $post = $this->createPost('A Post', 'Some post content.');

        $this->addCategoryToPost($post, $cat);

        $this->assertEquals(
            '1 posts', // Yes, I know...
            trim($this->browser()->get('/category/')->filter("#term-{$cat} .post-count")->text())
        );

        $post2 = $this->createPost('Another Post', 'Some more post content.');

        $this->addCategoryToPost($post2, $cat);

        $this->assertEquals(
            '2 posts',
            trim($this->browser()->get('/category/')->filter("#term-{$cat} .post-count")->text())
        );
    }

    /** @test */
    public function test_get_term_description()
    {
        // Alias of get_term_content()...
    }

    /** @test */
    public function test_get_term_id()
    {
        $cat = $this->createCategory('A Category');
        $post = $this->createPost('A Post', 'Some post content.');

        $this->addCategoryToPost($post, $cat);

        $this->browser()
            ->get('/category/')
            ->assertSee("id=\"term-{$cat}\"") // Article ID.
            ->assertSee("ta-term-{$cat}"); // One of the article classes.
    }

    /** @test */
    public function test_get_term_permalink()
    {
        $cat = $this->createCategory('A Category');
        $post = $this->createPost('A Post', 'Some post content.');

        $this->addCategoryToPost($post, $cat);

        $response = $this->browser()->get('/category/');

        $this->assertEquals(
            $this->browserBaseUri() . '/category/a-category/',
            $response->filter("#term-{$cat} .entry-title a")->attr('href')
        );
    }

    /** @test */
    public function test_get_term_taxonomy()
    {
        // Used by get_term_class().
        $cat = $this->createCategory('A Category');
        $post = $this->createPost('A Post', 'Some post content.');

        $this->addCategoryToPost($post, $cat);

        $response = $this->browser()->get('/category/');

        $this->assertStringContainsString(
            'ta-term-taxonomy-category',
            $response->filter("#term-{$cat}")->attr('class')
        );
    }

    /** @test */
    public function test_get_term_title()
    {
        $cat = $this->createCategory('A Category');
        $post = $this->createPost('A Post', 'Some post content.');

        $this->addCategoryToPost($post, $cat);

        $response = $this->browser()->get('/category/');

        $this->assertEquals(
            'A Category',
            trim($response->filter("#term-{$cat} .entry-title")->text())
        );
    }

    /** @test */
    public function test_get_terms_pagination()
    {
    }

    /** @test */
    public function test_have_terms()
    {
    }

    /** @test */
    public function test_is_terms_archive()
    {
        // Not used by theme...
    }

    /** @test */
    public function test_the_term()
    {
        // Might be a bit tricky to test...
    }

    /** @test */
    public function test_the_term_class()
    {
    }
}
