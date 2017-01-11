<?php

namespace Drupal\Tests\social_feed\Functional;

use Dotenv\Dotenv;
use Drupal\Tests\BrowserTestBase;

/**
 * Class TwitterPostCollectorTest
 *
 * @group social_feed
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class TwitterPostCollectorTest extends BrowserTestBase {

  public static $modules = ['block', 'social_feed'];
  protected $numberOfPosts = 10;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $dotenv = new Dotenv(__DIR__);
    $dotenv->load();
    $this->config('social_feed.twittersettings')
      ->set('consumer_key', getenv('social_feed.twitter.consumer_key'))
      ->set('consumer_secret', getenv('social_feed.twitter.consumer_secret'))
      ->set('access_token', getenv('social_feed.twitter.access_token'))
      ->set('access_token_secret', getenv('social_feed.twitter.access_token_secret'))
      ->set('tweets_count', $this->numberOfPosts)
      ->save();
    $this->drupalPlaceBlock('twitter_post');
  }

  /**
   * Tests the correct number of Twitter posts are shown.
   *
   * @test
   */
  public function it_displays_the_correct_number_of_twitter_posts() {
    $this->drupalGet('internal:/');
    $this->assertSession()->elementsCount('css', '.tw-tweet', $this->numberOfPosts);
  }

}
