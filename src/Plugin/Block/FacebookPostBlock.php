<?php

namespace Drupal\social_feed\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\social_feed\Services\FacebookPostCollector;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'FacebookPostBlock' block.
 *
 * @Block(
 *  id = "facebook_post",
 *  admin_label = @Translation("Facebook Post Block"),
 * )
 */
class FacebookPostBlock extends BlockBase implements ContainerFactoryPluginInterface {

  protected $facebook;
  protected $config;

  /**
   * {@inheritdoc}
   */
  public function __construct($configuration, $plugin_id, $plugin_definition, FacebookPostCollector $facebook, ConfigFactoryInterface $config) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->facebook = $facebook;
    $this->config = $config->get('social_feed.facebooksettings');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $items = [];
    $post_types = $this->config->get('all_types');
    if (!$post_types) {
      $post_types = $this->config->get('post_type');
    }
    $posts = $this->facebook->getPosts(
      $this->config->get('page_name'),
      $post_types,
      $this->config->get('no_feeds')
    );
    foreach ($posts as $post) {
      $items[] = [
        '#theme' => ['social_feed_facebook_post__' . $post['type'], 'social_feed_facebook_post'],
        '#post' => $post,
        '#cache' => [
          // Cache for 1 hour.
          'max-age' => 60 * 60,
          'cache tags' => $this->config->getCacheTags(),
          'context' => $this->config->getCacheContexts(),
        ],
      ];
    }
    $build['posts'] = [
      '#theme' => 'item_list',
      '#items' => $items,
    ];

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('social_feed.facebook'),
      $container->get('config.factory')
    );
  }

}
