<?php

namespace Drupal\Tests\social_feed\Functional;

use Dotenv\Dotenv;
use Drupal\Core\Url;
use Drupal\Tests\BrowserTestBase;

/**
 * Class FacebookPostCollectorTest
 *
 * @group social_feed
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class FacebookPostCollectorTest extends BrowserTestBase {
  public static $modules = ['block', 'social_feed'];

  protected $numberOfPosts = 30;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $dotenv = new Dotenv(__DIR__);
    $dotenv->load();
    $this->config('social_feed.facebooksettings')
      ->set('page_name', getenv('social_feed.facebook.page_name'))
      ->set('app_id', getenv('social_feed.facebook.app_id'))
      ->set('secret_key', getenv('social_feed.facebook.secret_key'))
      ->set('no_feeds', $this->numberOfPosts)
      ->save();
    $this->drupalPlaceBlock('facebook_post');
  }

  /**
   * Tests that the Facebook block retrieves the correct number of posts.
   *
   * @test
   */
  public function it_displays_the_correct_number_posts() {
    $this->drupalGet(Url::fromUri('internal:/'));
    $this->assertSession()->elementsCount('css', '.fb-message', $this->numberOfPosts);
  }

}
