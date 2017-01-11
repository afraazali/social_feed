<?php

namespace Drupal\Tests\social_feed\Functional;

use Dotenv\Dotenv;
use Drupal\Tests\BrowserTestBase;

/**
 * Class InstagramPostCollectorTest
 *
 * @group social_feed
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class InstagramPostCollectorTest extends BrowserTestBase {

  public static $modules = ['block', 'social_feed'];

  protected $numberOfPosts = 2;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $dotenv = new Dotenv(__DIR__);
    $dotenv->load();
    $this->config('social_feed.instagramsettings')
      ->set('client_id', getenv('social_feed.instagram.client_id'))
      ->set('access_token', getenv('social_feed.instagram.access_token'))
      ->set('picture_count', $this->numberOfPosts)
      ->save();
    $this->drupalPlaceBlock('instagram_post');
  }

  /**
   * Tests that the Instagram block shows the correct number of posts.
   *
   * @test
   */
  public function it_can_display_the_correct_number_of_instagram_posts() {
    $this->drupalGet('internal:/');
    $this->assertSession()->elementsCount('css', '.instagram-post', $this->numberOfPosts);
  }

}
