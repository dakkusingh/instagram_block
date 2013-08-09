<?php

/**
 * @file
 * Contains \Drupal\instagram_block\Form\InstagramBlockForm.
 */

namespace Drupal\instagram_block\Form;

use Drupal\system\SystemConfigFormBase;

/**
 * Configure instagram_block settings for this site.
 */
class InstagramBlockForm extends SystemConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'instagram_block_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state) {
    $form = array();

    $content = 'To configure your instagram account you need to authorise your account. To do this, click ';
    $path = 'https://instagram.com/oauth/authorize/';
    $options = array(
      'query' => array(
        'client_id' => '759ec610e0c1416baa8a8a6b41552087',
        'redirect_uri' => 'http://instagram.yanniboi.com/configure/instagram',
        'response_type' => 'code',
      ),
      'attributes' => array(
        'target' => '_blank',
      ),
    );

    $content .= l('here', $path, $options);
    $content .= '.';

    $form['authorise'] = array(
      '#markup' => $content,
    );

    // Create an array of empty keys to be used in storage variable has not been set.
    $empty = array(
      'user_id' => '',
      'access_token' => '',
      'count' => '',
      'width' => '',
      'height' => '',
    );

    // Store data from variable in $form for now.
    $form['#data'] = variable_get('instagram_block_data', $empty);

    $form['user_id'] = array(
      '#type' => 'textfield',
      '#title' => t('User Id'),
      '#description' => t('Your unique Instagram user id. Eg. 460786510'),
      '#default_value' => isset($form['#data']['user_id']) ? $form['#data']['user_id'] : '',
    );

    $form['access_token'] = array(
      '#type' => 'textfield',
      '#title' => t('Access Token'),
      '#description' => t('Your Instagram access token. Eg. 460786509.ab103e5.a54b6834494643588d4217ee986384a8'),
      '#default_value' => isset($form['#data']['access_token']) ? $form['#data']['access_token'] : '',
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    if (isset($form_state['values'])) {
      variable_set('instagram_block_data', $form_state['values']);
    }

    parent::submitForm($form, $form_state);
  }

}
