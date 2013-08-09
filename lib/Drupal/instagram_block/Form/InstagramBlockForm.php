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

    // Get the block configuration.
    $block_settings = array();
    foreach (list_themes() as $theme) {
      if ($block = entity_load('block', $theme->name . '.instagramblock')) {

        $block_settings[] = array(
          'settings' => $block->get('settings'),
          'block' => $block,
        );
      }
    }
    $block_settings = reset($block_settings);

    // Set the block its settings to the form for use later.
    $form['#data'] = $block_settings['settings'];
    $form['#block'] = $block_settings['block'];

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
      $block = $form['#block'];
      $block->getPlugin()->setConfigurationValue('user_id', $form_state['values']['user_id']);
      $block->getPlugin()->setConfigurationValue('access_token', $form_state['values']['access_token']);
      $block->save();
    }

    parent::submitForm($form, $form_state);
  }

}
