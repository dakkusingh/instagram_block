<?php

/**
 * @file
 * Contains \Drupal\instagram_block\Plugin\Block\InstagramBlockBlock.
 */

namespace Drupal\instagram_block\Plugin\Block;

use Drupal\block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Component\Annotation\Plugin;
use Drupal\Core\Annotation\Translation;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an Instagram block.
 *
 * @Plugin(
 *   id = "instagram_block_block",
 *   admin_label = @Translation("Instagram block"),
 *   module = "instagram_block"
 * )
 */
class InstagramBlockBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, array $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition, $container->get('request'));
  }

  /**
   * Overrides \Drupal\block\BlockBase::access().
   */
  public function access() {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function fetchData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
  }

  public function buildConfigurationForm(array $form, array &$form_state) {
    $form = array();
    $configuration = $this->configuration;

    $form['user_id'] = array(
      '#type' => 'textfield',
      '#title' => t('User Id'),
      '#description' => t('Your unique Instagram user id. Eg. 460786510'),
      '#default_value' => isset($configuration['user_id']) ? $configuration['user_id'] : '',
     );

    $form['access_token'] = array(
      '#type' => 'textfield',
      '#title' => t('Access Token'),
      '#description' => t('Your Instagram access token. Eg. 460786509.ab103e5.a54b6834494643588d4217ee986384a8'),
      '#default_value' => isset($configuration['access_token']) ? $configuration['access_token'] : '',
    );

    $form['count'] = array(
      '#type' => 'textfield',
      '#title' => t('Number of images to display.'),
      '#default_value' => isset($configuration['count']) ? $configuration['count'] : '',
    );

    $form['width'] = array(
      '#type' => 'textfield',
      '#title' => t('Image width in pixels.'),
      '#default_value' => isset($configuration['width']) ? $configuration['width'] : '',
    );

    $form['height'] = array(
      '#type' => 'textfield',
      '#title' => t('Image height in pixels.'),
      '#default_value' => isset($configuration['height']) ? $configuration['height'] : '',
    );

    return parent::buildConfigurationForm($form, $form_state);
  }
  /**
   * {@inheritdoc}
   *
   * Most block plugins should not override this method. To add submission
   * handling for a specific block type, override BlockBase::blockSubmit().
   *
   * Save the form values to the block configuration array.
   *
   * @see \Drupal\block\BlockBase::blockSubmit()
   */
  public function submitConfigurationForm(array &$form, array &$form_state) {

    if (!form_get_errors()) {
      $this->configuration['user_id'] = $form_state['values']['user_id'];
      $this->configuration['access_token'] = $form_state['values']['access_token'];
      $this->configuration['count'] = $form_state['values']['count'];
      $this->configuration['width'] = $form_state['values']['width'];
      $this->configuration['height'] = $form_state['values']['height'];
    }

    parent::submitConfigurationForm($form, $form_state);
  }


  /**
   * {@inheritdoc}
   */
  public function build() {
    $configuration = $this->configuration;
    // Build a render array to return the Instagram Images.
    $content = array();

    $url = "https://api.instagram.com/v1/users/" . $configuration['user_id'] . "/media/recent/?access_token=" . $configuration['access_token'] . "&count=" . $configuration['count'];
    $result = $this->fetchData($url);
    $result = json_decode($result);
    foreach ($result->data as $post) {
      $content['children'][$post->id] = array(
        '#markup' => '',
        '#theme' => 'instagram_block_image',
        '#data' => $post,
        '#href' => $post->link,
        '#src' => $post->images->thumbnail->url,
        '#width' => $configuration['width'],
        '#height' => $configuration['height'],
        '#attached' => array(
          'css' => array(
            drupal_get_path('module', 'instagram_block') . '/css/block.css'
          ),
        ),
      );
    }
    $block['subject'] = 'Instagram Block';
    $block['content'] = $content;

    return $block;
  }
}
