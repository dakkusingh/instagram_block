<?php

/**
 * @file
 * Contains \Drupal\instagram_block\Plugin\Block\InstagramBlockBlock.
 */

namespace Drupal\instagram_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use GuzzleHttp\Exception\RequestException;
use Drupal\Core\Url;

/**
 * Provides an Instagram block.
 *
 * @Block(
 *   id = "instagram_block_block",
 *   admin_label = @Translation("Instagram block"),
 *   category = @Translation("Social")
 * )
 */
class InstagramBlockBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The HTTP client to fetch the feed data with.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * Constructs a InstagramBlockBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \GuzzleHttp\Client $http_client
   *   The Guzzle HTTP client.
   * @param ConfigFactory $config_factory
   *   The factory for configuration objects.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, Client $http_client, ConfigFactory $config_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->httpClient = $http_client;
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('http_client'),
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function settings() {
    return array(
      'width' => '',
      'height' => '',
      'count' => '',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['count'] = array(
      '#type' => 'textfield',
      '#title' => t('Number of images to display.'),
      '#default_value' => isset($this->configuration['count']) ? $this->configuration['count'] : 4,
    );

    $form['width'] = array(
      '#type' => 'textfield',
      '#title' => t('Image width in pixels.'),
      '#default_value' => isset($this->configuration['width']) ? $this->configuration['width'] : '',
    );

    $form['height'] = array(
      '#type' => 'textfield',
      '#title' => t('Image height in pixels.'),
      '#default_value' => isset($this->configuration['height']) ? $this->configuration['height'] : '',
    );

    $image_options = array(
      'thumbnail' => t('Thumbnail (150x150)'),
      'low_resolution' => t('Low (320x320)'),
      'standard_resolution' => t('Standard (640x640)'),
    );

    $form['img_resolution'] = array(
      '#type' => 'select',
      '#title' => t('Image resolution'),
      '#description' => t('Choose the quality of the images you would like to display.'),
      '#default_value' => isset($this->configuration['img_resolution']) ? $this->configuration['img_resolution'] : 'thumbnail',
      '#options' => $image_options,
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockValidate($form, FormStateInterface $form_state) {
    // @TODO This is postponed until the resolution of [#2537732]
    //if (!is_numeric($form_state->getValue('count'))) {
    //  $form_state->setErrorByName('count', $this->t('Count must be numeric'));
    //}
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    if ($form_state->hasAnyErrors()) {
      return;
    }
    else {
      $this->configuration['count'] = $form_state->getValue('count');
      $this->configuration['width'] = $form_state->getValue('width');
      $this->configuration['height'] = $form_state->getValue('height');
      $this->configuration['img_resolution'] = $form_state->getValue('img_resolution');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Build a render array to return the Instagram Images.
    $build = array();
    $configuration = $this->configFactory->get('instagram_block.settings')->get();

    // If no configuration was saved, don't attempt to build block.
    if (empty($configuration['user_id']) || empty($configuration['access_token'])) {
      // @TODO Display a message instructing user to configure module.
      return $build;
    }

    // Build url for http request.
    $uri = "https://api.instagram.com/v1/users/{$configuration['user_id']}/media/recent/";
    $options = [
      'query' => [
        'access_token' => $configuration['access_token'],
        'count' => $this->configuration['count'],
      ],
    ];
    $url = Url::fromUri($uri, $options)->toString();

    // Get the instagram images and decode.
    $result = $this->_fetchData($url);
    if (!$result) {
      return $build;
    }

    foreach ($result['data'] as $post) {
      $build['children'][$post['id']] = array(
        '#theme' => 'instagram_block_image',
        '#data' => $post,
        '#href' => $post['link'],
        '#src' => $post['images'][$this->configuration['img_resolution']]['url'],
        '#width' => $this->configuration['width'],
        '#height' => $this->configuration['height'],
      );
    }

    // Add css.
    if (!empty($build)) {
      $build['#attached']['library'][] = 'instagram_block/instagram_block';
    }

    return $build;
  }

  /**
   * Sends a http request to the Instagram API Server
   *
   * @param string $url
   *   URL for http request.
   *
   * @return bool|mixed
   *   The encoded response containing the instagram images or FALSE.
   */
  protected function _fetchData($url) {
    try {
      $response = $this->httpClient->get($url, array('headers' => array('Accept' => 'application/json')));
      $data = json_decode($response->getBody(), TRUE);
      if (empty($data)) {
        return FALSE;
      }

      return $data;
    }
    catch (RequestException $e) {
      return FALSE;
    }
  }

}
