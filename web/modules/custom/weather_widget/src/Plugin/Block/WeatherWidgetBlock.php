<?php
namespace Drupal\weather_widget\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\weather_widget\Service\WeatherService;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * @Block(
 *   id = "weather_widget_block",
 *   admin_label = @Translation("Weather Forecast Block")
 * )
 */
class WeatherWidgetBlock extends BlockBase implements ContainerFactoryPluginInterface {

  protected $weatherService;
  protected $configFactory;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, WeatherService $weather_service, ConfigFactoryInterface $config_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->weatherService = $weather_service;
    $this->configFactory = $config_factory;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('weather_widget.weather_service'),
      $container->get('config.factory')
    );
  }

  public function defaultConfiguration() {
    return [
      'city' => '',
      'unit' => '',
      'theme' => 'light',
    ];
  }

  public function blockForm($form, FormStateInterface $form_state) {
    $form['city'] = [
      '#type' => 'textfield',
      '#title' => 'City (optional)',
      '#default_value' => $this->configuration['city'],
    ];

    $form['unit'] = [
      '#type' => 'select',
      '#title' => 'Unit',
      '#options' => ['metric' => 'Celsius', 'imperial' => 'Fahrenheit'],
      '#default_value' => $this->configuration['unit'],
    ];

    $form['theme'] = [
      '#type' => 'select',
      '#title' => 'Theme',
      '#options' => ['light' => 'Light', 'dark' => 'Dark'],
      '#default_value' => $this->configuration['theme'],
    ];

    return $form;
  }

  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['city'] = $form_state->getValue('city');
    $this->configuration['unit'] = $form_state->getValue('unit');
    $this->configuration['theme'] = $form_state->getValue('theme');
  }

  public function build() {
    $config = $this->configFactory->get('weather_widget.settings');

    $city = $this->configuration['city'] ?: $config->get('default_city');
    $unit = $this->configuration['unit'] ?: $config->get('default_unit');

    $data = $this->weatherService->getWeather($city, $unit);

    return [
      '#theme' => 'weather_widget_block',
      '#data' => $data,
      '#theme_style' => $this->configuration['theme'],
      '#cache' => [
        'max-age' => 600,
        'tags' => ['weather_data'],
      ],
      '#attached' => [
        'library' => ['weather_widget/forecast-ui'],
      ],
    ];
  }
}