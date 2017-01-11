<?php

namespace Drupal\social_feed\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\social_feed\Services\TwitterPostCollector;

/**
 * Provides a 'TwitterPostBlock' block.
 *
 * @Block(
 *  id = "twitter_post",
 *  admin_label = @Translation("Twitter Post Block"),
 * )
 */
class TwitterPostBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\social_feed\Services\TwitterPostCollector definition.
   *
   * @var \Drupal\social_feed\Services\TwitterPostCollector
   */
  protected $twitter;

  /**
   * Config factory.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * Construct.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   */
  public function __construct(
        array $configuration,
        $plugin_id,
        $plugin_definition,
        TwitterPostCollector $social_feed_twitter,
        ConfigFactoryInterface $config
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->twitter = $social_feed_twitter;
    $this->config = $config->get('social_feed.twittersettings');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('social_feed.twitter'),
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $posts = $this->twitter->getPosts($this->config->get('tweets_count'));
    foreach ($posts as $post) {
      $items[] = [
        '#theme' => 'social_feed_twitter_post',
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

}
