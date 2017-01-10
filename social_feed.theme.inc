<?php

/**
 * @file
 * Theme preprocessors.
 */

use Carbon\Carbon;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Preprocess social_feed_facebook_post.
 *
 * @see social_feed_theme()
 */
function template_preprocess_social_feed_facebook_post(&$variables) {
  $use_facebook_hashtag = Drupal::config('social_feed.facebooksettings')->get('hashtag');
  $should_display_time = Drupal::config('social_feed.facebooksettings')->get('time_stamp');
  $teaser_text = Drupal::config('social_feed.facebooksettings')->get('teaser_text');
  $post = &$variables['post'];
  if (isset($post['message'])) {
    $post['message'] = substr($post['message'], 0, Drupal::config('social_feed.facebooksettings')->get('trim_length'));
  }

  if (FALSE == empty($teaser_text) && FALSE == empty($post['link'])) {
    $post['link'] = Link::fromTextAndUrl(t('@teaser_text', array('@teaser_text' => $teaser_text)), Url::fromUri($post['link'], array('attributes' => array('target' => '_blank'))))->toString();
  }

  if ($use_facebook_hashtag) {
    $post['message'] = preg_replace_callback(
      '/#(\\w+)/',
      function ($hash) {
        return Link::fromTextAndUrl($hash[0], Url::fromUri('https:facebook.com/hashtag/' . $hash[1], array('attributes' => array('target' => '_blank'))))->toString();
      },
      $post['message']
    );
  }
  if ($should_display_time) {
    $variables['should_display_time'] = $should_display_time;
    $formatted_date = new DateTime($post['created_time']);
    $post['created_time'] = $formatted_date->format(Drupal::config('social_feed.facbeooksettings')->get('time_format'));
  }

  $post['message'] = ['#markup' => $post['message']];
}

/**
 * Preprocess social_feed_twitter_post.
 *
 * @see social_feed_theme()
 */
function template_preprocess_social_feed_twitter_post(&$variables) {
  $display_time = Drupal::config('social_feed.twittersettings')->get('time_stamp');
  $display_date_twitter_style = Drupal::config('social_feed.twittersettings')->get('time_ago');
  $twitter_hash_tag = Drupal::config('social_feed.twittersettings')->get('hashtag');
  $time_format = Drupal::config('social_feed.twittersettings')->get('time_format');
  $trim_length = Drupal::config('social_feed.twittersettings')->get('trim_length');
  $teaser_text = Drupal::config('social_feed.twittersettings')->get('teaser_text');

  $post = &$variables['post'];

  $post->username = $post->user->screen_name;
  $post->full_username = 'http://twitter.com/' . $post->username;
  preg_match_all('#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', $post->text, $extra_links);
  foreach ($extra_links[0] as $extra_link) {
    $post->text = str_replace($extra_link, Link::fromTextAndUrl($extra_link, Url::fromUri($extra_link, ['attributes' => ['target' => '_blank']]))->toString(), $post->text);
  }
  if (isset($post->text)) {
    $post->tweet = substr(rtrim($post->text), 0, $trim_length);
  }
  if (FALSE == empty($teaser_text)) {
    if (isset($post->entities->media)) {
      $post->tweet_url = Link::fromTextAndUrl(t('@teaser_text', array('@teaser_text' => $teaser_text)), Url::fromUri($post->entities->media[0]->url, ['attributes' => ['target' => '_blank']]))->toString();
    }
  }
  if ($display_time) {
    $formatted_twitter_date = new DateTime($post->created_at);
    if ($display_date_twitter_style) {
      $post->twitter_date = social_feed_time_elapsed_string($formatted_twitter_date);
    }
    else {
      $post->twitter_date = $formatted_twitter_date->format($time_format);
    }
  }
  if ($twitter_hash_tag) {
    $post->tweet = preg_replace_callback(
      '/#(\\w+)|@(\\w+)/',
      function ($hash) {
        if ($hash[0][0] == '#') {
          return Link::fromTextAndUrl($hash[0], Url::fromUri('https:twitter.com/hashtag/' . $hash[1], array('attributes' => array('target' => '_blank'))))->toString();
        }
        if ($hash[0][0] == '@') {
          return Link::fromTextAndUrl($hash[0], Url::fromUri('https:twitter.com/' . $hash[2], array('attributes' => array('target' => '_blank'))))->toString();
        }
      },
      $post->tweet
    );
  }
  $post->tweet = ['#markup' => $post->tweet];
}

/**
 * Preprocess social_feed_instagram_post_video.
 *
 * @see social_feed_theme()
 */
function template_preprocess_social_feed_instagram_post_video(&$variables) {
  social_feed_preprocess_social_feed_instagram_post($variables);
}

/**
 * Preprocess social_feed_instagram_post_image.
 *
 * @see social_feed_theme()
 */
function template_preprocess_social_feed_instagram_post_image(&$variables) {
  social_feed_preprocess_social_feed_instagram_post($variables);
}

/**
 * Preprocess Instagram posts.
 *
 * @see template_preprocess_social_feed_instagram_post_video()
 * @see template_preprocess_social_feed_instagram_post_image()
 */
function social_feed_preprocess_social_feed_instagram_post(&$variables) {
  $instagram_post_link = Drupal::config('social_feed.instagramsettings')->get('post_link');
  $post = &$variables['post'];

  if ($instagram_post_link) {
    $post['post_url'] = $post['raw']->link;
  }
}

/**
 * Displays date in Twitter format.
 */
function social_feed_time_elapsed_string($datetime) {
  return Carbon::instance($datetime)->diffForHumans();
}
