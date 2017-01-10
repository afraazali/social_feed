<?php

namespace Drupal\social_feed\Services;

use Drupal\Core\Config\ConfigFactoryInterface;
use Facebook\Facebook;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FacebookPostCollector.
 *
 * @package Drupal\social_feed
 */
class FacebookPostCollector {

  /**
   * Field names to retrieve from Facebook.
   *
   * @var array
   */
  protected $fields = [
    'link',
    'message',
    'created_time',
    'picture',
    'type',
  ];

  /**
   * Facebook application id.
   *
   * @var string
   */
  protected $appId;

  /**
   * Facebook application secret.
   *
   * @var string
   */
  protected $appSecret;

  /**
   * Facebook Client.
   *
   * @var \Facebook\Facebook
   */
  private $facebook;

  /**
   * FacebookPostCollector constructor.
   *
   * @param ConfigFactoryInterface $configFactory
   *   Config factory.
   * @param Facebook|null $facebook
   *   Facebook client.
   */
  public function __construct(ConfigFactoryInterface $configFactory, Facebook $facebook = NULL) {
    $config          = $configFactory->get('social_feed.facebooksettings');
    $this->appId     = $config->get('app_id');
    $this->appSecret = $config->get('secret_key');
    $this->facebook  = $facebook;
  }

  /**
   * Set the Facebook client.
   */
  public function setFacebookClient() {
    if (NULL === $this->facebook) {
      $this->facebook = new Facebook([
        'app_id'                => $this->appId,
        'app_secret'            => $this->appSecret,
        'default_graph_version' => 'v2.8',
        'default_access_token'  => $this->defaultAccessToken(),
      ]);
    }
  }

  /**
   * Fetch Facebook posts from a given feed.
   *
   * @param string $page_name
   *   The name of the page to fetch results from.
   * @param string $post_types
   *   The post types to filter for.
   * @param int $num_posts
   *   The number of posts to return.
   *
   * @return array
   *   An array of Facebook posts.
   */
  public function getPosts($page_name, $post_types, $num_posts = 10) {
    $posts      = [];
    $post_count = 0;
    $url        = $page_name . $this->getFacebookFeedUrl($num_posts);
    do {
      $response = $this->facebook->get($url);
      // Ensure not caught in an infinite loop if there's no next page.
      $url = NULL;
      if ($response->getHttpStatusCode() == Response::HTTP_OK) {
        $data       = json_decode($response->getBody(), TRUE);
        $posts      = array_merge($this->extractFacebookFeedData($post_types, $data['data']), $posts);
        $post_count = count($posts);
        if ($post_count < $num_posts && isset($data['paging']['next'])) {
          $url = $data['paging']['next'];
        }
      }
    } while ($post_count < $num_posts || NULL != $url);
    return array_slice($posts, 0, $num_posts);
  }

  /**
   * Extract information from the Facebook feed.
   *
   * @param string $post_types
   *   The type of posts to extract.
   * @param array $data
   *   An array of data to extract information from.
   *
   * @return array
   *   An array of posts.
   */
  protected function extractFacebookFeedData($post_types, array $data) {
    $posts = array_map(function ($post) {
      return $post;
    }, $data);

    // Filtering needed.
    if (TRUE == is_string($post_types)) {
      $posts = array_filter($posts, function ($post) use ($post_types) {
        return $post['type'] === $post_types;
      });
      return $posts;
    }
    return $posts;
  }

  /**
   * Generate the Facebook access token.
   *
   * @return string
   *   The access token.
   */
  protected function defaultAccessToken() {
    return $this->appId . '|' . $this->appSecret;
  }

  /**
   * Create the Facebook feed URL.
   *
   * @param int $num_posts
   *   The number of posts to return.
   *
   * @return string
   *   The feed URL with the appended fields to retrieve.
   */
  protected function getFacebookFeedUrl($num_posts) {
    return '/feed?fields=' . implode(',', $this->fields) . '&limit=' . $num_posts;
  }

}
