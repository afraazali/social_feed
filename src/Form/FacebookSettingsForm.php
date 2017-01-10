<?php

namespace Drupal\social_feed\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class FacebookSettingsForm.
 *
 * @package Drupal\social_feed\Form
 */
class FacebookSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'social_feed.facebooksettings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'facebook_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('social_feed.facebooksettings');
    $post_type_options = ['link', 'status', 'photo', 'video'];

    $form['page_name'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Facebook Page Name'),
      '#default_value' => $config->get('page_name'),
      '#description'   => $this->t('eg. If your Facebook page URL is this http://www.facebook.com/YOUR_PAGE_NAME, <br />then you just need to add this YOUR_PAGE_NAME above.'),
      '#size'          => 60,
      '#maxlength'     => 100,
      '#required'      => TRUE,
    ];

    $form['app_id'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Facebook App ID'),
      '#default_value' => $config->get('app_id'),
      '#size'          => 60,
      '#maxlength'     => 100,
      '#required'      => TRUE,
    ];

    $form['secret_key'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Facebook Secret Key'),
      '#default_value' => $config->get('secret_key'),
      '#size'          => 60,
      '#maxlength'     => 100,
      '#required'      => TRUE,
    ];

    $form['no_feeds'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Number of Feeds'),
      '#default_value' => $config->get('no_feeds'),
      '#size'          => 60,
      '#maxlength'     => 60,
    ];

    $form['all_types'] = [
      '#type'          => 'checkbox',
      '#title'         => $this->t('Show all post types'),
      '#default_value' => $config->get('all_types'),
    ];

    $form['post_type'] = [
      '#type'          => 'select',
      '#title'         => 'Select your post type(s) to show',
      '#default_value' => $config->get('post_type'),
      '#options'       => array_combine($post_type_options, $post_type_options),
      '#empty_option'  => $this->t('- Select -'),
      '#states'        => [
        'visible'  => [
          ':input[name="all_types"]' => ['checked' => FALSE],
        ],
        'required' => [
          ':input[name="all_types"]' => ['checked' => FALSE],
        ],
      ],
    ];

    $form['display_pic'] = [
      '#type'          => 'checkbox',
      '#title'         => $this->t('Show Post Picture'),
      '#default_value' => $config->get('display_pic', FALSE),
      '#states'        => [
        'visible' => [
          ':input[name="post_type"]' => ['value' => 2],
        ],
      ],
    ];

    $form['display_video'] = [
      '#type'          => 'checkbox',
      '#title'         => $this->t('Show Post Video'),
      '#default_value' => $config->get('display_video', FALSE),
      '#states'        => [
        'visible' => [
          ':input[name="post_type"]' => ['value' => 3],
        ],
      ],
    ];

    $form['trim_length'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Trim Length'),
      '#default_value' => $config->get('trim_length'),
      '#size'          => 60,
      '#maxlength'     => 60,
    ];

    $form['teaser_text'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Teaser Text'),
      '#default_value' => $config->get('teaser_text'),
      '#size'          => 60,
      '#maxlength'     => 60,
    ];

    $form['hashtag'] = [
      '#type'          => 'checkbox',
      '#title'         => $this->t('Show Hashtag'),
      '#default_value' => $config->get('hashtag', FALSE),
    ];

    $form['time_stamp'] = [
      '#type'          => 'checkbox',
      '#title'         => $this->t('Show Date/Time'),
      '#default_value' => $config->get('time_stamp', FALSE),
    ];

    $form['time_format'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Date/Time format'),
      '#default_value' => $config->get('time_format', 'd-M-Y'),
      '#description'   => $this->t('You can check for PHP Date Formats <a href="@datetime" target="@blank">here</a>', [
        '@datetime' => 'http://php.net/manual/en/function.date.php',
        '@blank'    => '_blank',
      ]),
      '#size'          => 60,
      '#maxlength'     => 100,
      '#states'        => [
        'visible' => [
          ':input[name="time_stamp"]' => ['checked' => TRUE],
        ],
      ],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('social_feed.facebooksettings');
    foreach ($form_state->getValues() as $key => $value) {
      $config->set($key, $value);
    }
    $config->save();
    parent::submitForm($form, $form_state);
  }

}