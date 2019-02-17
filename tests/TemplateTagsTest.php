<?php

use SsnTestKit\ManagesPosts;
use SsnTestKit\ManagesTerms;
use SsnTestKit\ManagesUserSessions;

class TemplateTagsTest extends TaTestCase
{
    use ManagesPosts,
        ManagesTerms,
        ManagesUserSessions;

    /** @test */
    public function test_get_current_term()
    {
        $this->activateTheme('ta-twentyfifteen-child');
        $this->activatePlugin('terms-archive');

        $cat = $this->createCategory('A Category');
        $post = $this->createPost('A Post', 'Some post content.');

        $this->addCategoriesToPost($post, $cat);

        $this->login();

        // The only (current) use of get_current_term() is in generating the term edit link.
        $editLinkSelector = ".ta-term-{$cat} .edit-link a";

        $this->assertEquals(
            sprintf(
                // @todo Check with WP core - Shouldn't this have been escaped?
                '%s/wp-admin/term.php?taxonomy=category&tag_ID=%s&post_type=post',
                $this->browserBaseUri(),
                $cat
            ),
            $this->browser()->get('/category/')->filter($editLinkSelector)->attr('href')
        );
    }

    /** @test */
    public function test_get_loop()
    {
        $this->markTestSkipped('Not currently used by theme');
    }

    /** @test */
    public function test_get_queried_taxonomy()
    {
        $this->markTestSkipped('Not currently used by theme');
    }

    /** @test */
    public function test_get_term_content()
    {
        $this->activateTheme('ta-twentyfifteen-child');
        $this->activatePlugin('terms-archive');

        $cat = $this->createCategory('A Category', 'Just a category description.');
        $post = $this->createPost('A Post', 'Some post content.');

        $this->addCategoriesToPost($post, $cat);

        $response = $this->browser()->get('/category/');

        $this->assertEquals(
            'Just a category description.',
            trim($response->filter("#term-{$cat} .entry-content")->text())
        );

        // get_term_description() is an alias of get_term_content() - does it need to be tested?
    }

    /** @test */
    public function test_get_term_count()
    {
        $this->activateTheme('ta-twentyfifteen-child');
        $this->activatePlugin('terms-archive');

        $cat = $this->createCategory('A Category');
        $post = $this->createPost('A Post', 'Some post content.');

        $this->addCategoriesToPost($post, $cat);

        $this->assertEquals(
            '1 posts', // Yes, I know...
            trim($this->browser()->get('/category/')->filter("#term-{$cat} .post-count")->text())
        );

        $post2 = $this->createPost('Another Post', 'Some more post content.');

        $this->addCategoriesToPost($post2, $cat);

        $this->assertEquals(
            '2 posts',
            trim($this->browser()->get('/category/')->filter("#term-{$cat} .post-count")->text())
        );
    }

    /** @test */
    public function test_get_term_id()
    {
        $this->activateTheme('ta-twentyfifteen-child');
        $this->activatePlugin('terms-archive');

        $cat = $this->createCategory('A Category');
        $post = $this->createPost('A Post', 'Some post content.');

        $this->addCategoriesToPost($post, $cat);

        $this->browser()
            ->get('/category/')
            ->assertSee("id=\"term-{$cat}\"") // Article ID.
            ->assertSee("ta-term-{$cat}"); // One of the article classes.
    }

    /** @test */
    public function test_get_term_permalink()
    {
        $this->activateTheme('ta-twentyfifteen-child');
        $this->activatePlugin('terms-archive');

        $cat = $this->createCategory('A Category');
        $post = $this->createPost('A Post', 'Some post content.');

        $this->addCategoriesToPost($post, $cat);

        $response = $this->browser()->get('/category/');

        $this->assertEquals(
            $this->browserBaseUri() . '/category/a-category/',
            $response->filter("#term-{$cat} .entry-title a")->attr('href')
        );
    }

    /** @test */
    public function test_get_term_taxonomy()
    {
        $this->activateTheme('ta-twentyfifteen-child');
        $this->activatePlugin('terms-archive');

        // Used by get_term_class().
        $cat = $this->createCategory('A Category');
        $post = $this->createPost('A Post', 'Some post content.');

        $this->addCategoriesToPost($post, $cat);

        $response = $this->browser()->get('/category/');

        $this->assertStringContainsString(
            'ta-term-taxonomy-category',
            $response->filter("#term-{$cat}")->attr('class')
        );
    }

    /** @test */
    public function test_get_term_title()
    {
        $this->activateTheme('ta-twentyfifteen-child');
        $this->activatePlugin('terms-archive');

        $cat = $this->createCategory('A Category');
        $post = $this->createPost('A Post', 'Some post content.');

        $this->addCategoriesToPost($post, $cat);

        $response = $this->browser()->get('/category/');

        $this->assertEquals(
            'A Category',
            trim($response->filter("#term-{$cat} .entry-title")->text())
        );
    }

    /** @test */
    public function test_get_terms_pagination()
    {
        $this->activateTheme('ta-twentyfifteen-child');
        $this->activatePlugin('terms-archive');

        [$postId] = $this->generatePosts(1);
        $categories = $this->generateCategories(9); // Plus "uncategorized" makes 10

        $this->addCategoriesToPost($postId, ...$categories);

        $this->assertCount(0, $this->browser()->get('/category/page/2/')->filter('.pagination'));

        $categories = $this->generateCategories(10); // Total of 20.

        $this->addCategoriesToPost($postId, ...$categories);

        $response = $this->browser()->get('/category/');

        $this->assertCount(1, $response->filter('.pagination'));

        // @todo Probably a little too implementation-specific...
        $this->assertCount(3, $response->filter('.pagination .page-numbers'));
        $this->assertEquals(
            'Page 1',
            $response->filter('.pagination .page-numbers.current')->text()
        );
        $this->assertStringEndsWith(
            '/category/page/2/',
            $response->filter('.pagination .page-numbers.next')->attr('href')
        );
    }

    /** @test */
    public function test_have_terms()
    {
        $this->markTestSkipped(
            'Not easily testable until # 12'
            . ' (https://github.com/ssnepenthe/terms-archive/issues/12) is fixed'
        );
    }

    /** @test */
    public function test_is_terms_archive()
    {
        $this->markTestIncomplete('Not currently used (directly) by theme');
    }

    /** @test */
    public function test_the_term()
    {
        $this->markTestIncomplete('Not sure how to approach testing this yet...');
    }

    /** @test */
    public function test_the_term_class()
    {
        // @todo See ViewsTest @ body class method
        $this->activateTheme('ta-twentyfifteen-child');
        $this->activatePlugin('terms-archive');

        [$postId] = $this->generatePosts(1);
        [$tagId] = $this->generateTags(1);

        $this->addTagsToPost($postId, $tagId);

        $termClass = $this->browser()->get('/tag/')->filter('article')->attr('class');

        $this->assertStringContainsString('ta-term', $termClass);
        $this->assertStringContainsString("ta-term-{$tagId}", $termClass);
        $this->assertStringContainsString('ta-term-taxonomy-post_tag', $termClass);

        $this->markTestIncomplete('Need to add tests for classes on hierarchical terms');

        // get_term_class() is essentially the same but responsible for actually echoing classes -
        // Should these be tested separately?
    }
}
