<?php

namespace Drupal\social_feed\Services;

use Abraham\TwitterOAuth\TwitterOAuth;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Class TwitterPostCollector.
 *
 * @package Drupal\social_feed\Services
 */
class TwitterPostCollector {

  /**
   * Twitter application consumer key.
   *
   * @var string
   */
  protected $consumerKey;

  /**
   * Twitter application consumer secret.
   *
   * @var string
   */
  protected $consumerSecret;

  /**
   * Twitter application access token.
   *
   * @var string
   */
  protected $accessToken;

  /**
   * Twitter application access token secret.
   *
   * @var string
   */
  protected $accessTokenSecret;

  /**
   * Twitter OAuth client.
   *
   * @var \Abraham\TwitterOAuth\TwitterOAuth
   */
  protected $twitter;

  /**
   * TwitterPostCollector constructor.
   *
   * @param ConfigFactoryInterface $configFactory
   *   Config factory.
   * @param TwitterOAuth|null $twitter
   *   Twitter OAuth Client.
   */
  public function __construct(ConfigFactoryInterface $configFactory, TwitterOAuth $twitter = NULL) {
    $config                  = $configFactory->get('social_feed.twittersettings');
    $this->consumerKey       = $config->get('consumer_key');
    $this->consumerSecret    = $config->get('consumer_secret');
    $this->accessToken       = $config->get('access_token');
    $this->accessTokenSecret = $config->get('access_token_secret');
    $this->twitter           = $twitter;
  }

  /**
   * Retrieve Tweets from the given accounts home page.
   *
   * @param int $count
   *   The number of posts to return.
   *
   * @return array
   *   An array of posts.
   */
  public function getPosts($count) {
    return $this->twitter->get('statuses/home_timeline', ['count' => $count]);
  }

  /**
   * Set the Twitter client.
   */
  public function setTwitterClient() {
    if (NULL === $this->twitter) {
      $this->twitter = new TwitterOAuth(
        $this->consumerKey,
        $this->consumerSecret,
        $this->accessToken,
        $this->accessTokenSecret
      );
    }
  }

}
