<?php

namespace Drupal\social_feed\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Class InstagramSettingsForm.
 *
 * @package Drupal\social_feed\Form
 */
class InstagramSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'social_feed.instagramsettings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'instagram_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('social_feed.instagramsettings');
    $form['header']['#markup'] = $this->t('To get Client ID you need to manage clients from your instagram account detailed information <a href="@admin" target="@blank">here</a>.', [
      '@admin' => Url::fromRoute('help.page',
        ['name' => 'social_feed'])->toString(), '@blank' => '_blank',
    ]);
    $form['client_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Client ID'),
      '#description' => $this->t('Client ID from Instagram account'),
      '#default_value' => $config->get('client_id'),
      '#size' => 60,
      '#maxlength' => 100,
      '#required' => TRUE,
    ];
    $form['redirect_uri'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Redirect URI'),
      '#description' => $this->t('Redirect URI from Instagram account'),
      '#default_value' => $config->get('redirect_uri'),
      '#size' => 60,
      '#maxlength' => 100,
      '#required' => TRUE,
    ];
    $form['auth_link'] = [
      '#type' => 'item',
      '#title' => $this->t('Generate Instagram Access Token'),
      '#description' => $this->t('Access this URL in your browser https://instagram.com/oauth/authorize/?client_id=&lt;your_client_id&gt;&redirect_uri=&lt;your_redirect_uri&gt;&response_type=token, you will get the access token.'),
      '#markup' => $this->t('Check <a href="@this" target="_blank">this</a> article.', ['@this' => Url::fromUri('http://jelled.com/instagram/access-token')->toString()]),
    ];
    $form['access_token'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Access Token'),
      '#default_value' => $config->get('access_token'),
      '#size' => 60,
      '#maxlength' => 100,
      '#required' => TRUE,
    ];
    $form['picture_count'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Picture Count'),
      '#default_value' => $config->get('picture_count'),
      '#size' => 60,
      '#maxlength' => 100,
    ];
    if ($config->get('access_token')) {
      $form['feed'] = [
        '#type' => 'item',
        '#title' => $this->t('Feed URL'),
        '#markup' => $this->t('https://api.instagram.com/v1/users/self/feed?access_token=@access_token&count=@picture_count',
          [
            '@access_token' => $config->get('access_token'),
            '@picture_count' => $config->get('picture_count'),
          ]
        ),
      ];
    }
    $form['picture_resolution'] = [
      '#type' => 'select',
      '#title' => $this->t('Picture Resolution'),
      '#default_value' => $config->get('picture_resolution'),
      '#options' => [
        'thumbnail' => $this->t('Thumbnail'),
        'low_resolution' => $this->t('Low Resolution'),
        'standard_resolution' => $this->t('Standard Resolution'),
      ],
    ];
    $form['post_link'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show post URL'),
      '#default_value' => $config->get('post_link'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('social_feed.instagramsettings');
    foreach ($form_state->getValues() as $key => $value) {
      $config->set($key, $value);
    }
    $config->save();
    parent::submitForm($form, $form_state);
  }

}
