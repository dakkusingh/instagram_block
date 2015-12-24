<?php

/**
 * @file
 * Contains \Drupal\instagram_block\Form\InstagramBlockForm.
 */

namespace Drupal\instagram_block\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;


/**
 * Configure instagram_block settings for this site.
 */
class InstagramBlockForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'instagram_block_config_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['instagram_block.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Get module configuration.
    $config = $this->config('instagram_block.settings');

    $options = [
      'query' => [
        'client_id' => '759ec610e0c1416baa8a8a6b41552087',
        'redirect_uri' => 'http://instagram.yanniboi.com/configure/instagram',
        'response_type' => 'code',
      ],
    ];

    $url = Url::fromUri('https://instagram.com/oauth/authorize/', $options);
    $link = Link::fromTextAndUrl('here', $url)->toRenderable();
    $link['#attributes']['target'] = '_blank';


    $form['authorise'] = array(
      '#markup' => t('To configure your instagram account you need to authorise your account. To do this, click %link.', array('%link' => render($link))),
    );

    $form['user_id'] = array(
      '#type' => 'textfield',
      '#title' => t('User Id'),
      '#description' => t('Your unique Instagram user id. Eg. 460786510'),
      '#default_value' => $config->get('user_id'),
    );

    $form['access_token'] = array(
      '#type' => 'textfield',
      '#title' => t('Access Token'),
      '#description' => t('Your Instagram access token. Eg. 460786509.ab103e5.a54b6834494643588d4217ee986384a8'),
      '#default_value' => $config->get('access_token'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $user_id = $form_state->getValue('user_id');
    $access_token = $form_state->getValue('access_token');

    // Get module configuration.
    $this->config('instagram_block.settings')
      ->set('user_id', $user_id)
      ->set('access_token', $access_token)
      ->save();

    parent::submitForm($form, $form_state);
  }

}
