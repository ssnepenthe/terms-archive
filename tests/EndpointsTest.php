<?php

use SsnTestKit\ResetsSite;
use SsnTestKit\ManagesPosts;
use SsnTestKit\ManagesTerms;

class EndpointsTest extends TaTestCase
{
    use ManagesPosts,
        ManagesTerms,
        ResetsSite;

    /** @test */
    public function it_can_list_categories_with_attached_posts()
    {
        $catOne = $this->createCategory('Category One');
        $catTwo = $this->createCategory('Category Two');
        $catThree = $this->createCategory('Category Three');

        $postOne = $this->createPost('Categorized Post One', 'Test categorized post one content');
        $postTwo = $this->createPost('Categorized Post Two', 'Test categorized post two content');

        $this->addCategoryToPost($postOne, $catOne);
        $this->addCategoryToPost($postTwo, $catTwo);

        $this->browser()
            ->get('/category/')
            ->assertOk()
            ->assertSeeInOrder([
                'Category One',
                'Category Two',
            ])
            ->assertDontSee('Category Three');
    }

    /** @test */
    public function it_can_list_tags_with_attached_posts()
    {
        $tagOne = $this->createTag('Tag One');
        $tagTwo = $this->createTag('Tag Two');
        $tagThree = $this->createTag('Tag Three');

        $postOne = $this->createPost('Tagged Post One', 'Test tagged post one content');
        $postTwo = $this->createPost('Tagged Post Two', 'Test tagged post two content');

        $this->addTagToPost($postOne, $tagOne);
        $this->addTagToPost($postTwo, $tagTwo);

        $this->browser()
            ->get('/tag/')
            ->assertOk()
            ->assertSeeInOrder([
                'Tag One',
                'Tag Two',
            ])
            ->assertDontSee('Tag Three');
    }
}
