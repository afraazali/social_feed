<?php

namespace Drupal\social_feed\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\social_feed\Services\InstagramPostCollector;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Config\ConfigFactory;

/**
 * Provides a 'InstagramPostBlock' block.
 *
 * @Block(
 *  id = "instagram_post",
 *  admin_label = @Translation("Instagram Post Block"),
 * )
 */
class InstagramPostBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\Core\Config\ConfigFactory definition.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $config;

  /**
   * Instagram Service.
   *
   * @var InstagramPostCollector
   */
  protected $instagram;

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
        ConfigFactory $config_factory,
        InstagramPostCollector $instagram
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->config = $config_factory->get('social_feed.instagramsettings');
    $this->instagram = $instagram;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('social_feed.instagram')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $posts = $this->instagram->getPosts($this->config->get('picture_count'), $this->config->get('picture_resolution'));

    foreach ($posts as $post) {
      $items[] = [
        '#theme' => 'social_feed_instagram_post_' . $post['raw']->type,
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
