<?php

namespace Drupal\weather_widget\Service;

use GuzzleHttp\ClientInterface;
use Drupal\Core\Site\Settings;
use Psr\Log\LoggerInterface;

/**
 *
 */
class WeatherService {

  protected ClientInterface $httpClient;
  protected Settings $settings;
  protected LoggerInterface $logger;

  public function __construct(
    ClientInterface $http_client,
    Settings $settings,
    LoggerInterface $logger,
  ) {
    $this->httpClient = $http_client;
    $this->settings = $settings;
    $this->logger = $logger;
  }

  /**
   * Fetch weather by city name.
   */
  public function getWeather(string $city, string $unit = 'metric'): array {
    return $this->makeRequest([
      'q' => $city,
      'units' => $unit,
    ]);
  }

  /**
   * Fetch weather by coordinates.
   */
  public function getWeatherByCoords(float $lat, float $lon, string $unit = 'metric'): array {
    if (!$lat || !$lon) {
      return ['error' => 'Invalid coordinates'];
    }

    return $this->makeRequest([
      'lat' => $lat,
      'lon' => $lon,
      'units' => $unit,
    ]);
  }

  /**
   * Core request handler.
   */
  private function makeRequest(array $query): array {
    $apiKey = $this->settings->get('weather_api_key');

    try {
      $response = $this->httpClient->request(
        'GET',
        'https://api.openweathermap.org/data/2.5/weather',
        [
          'query' => $query + ['appid' => $apiKey],
          'timeout' => 3,
        ]
      );

      $data = json_decode($response->getBody(), TRUE);

      return $this->formatResponse($data);
    }
    catch (\Exception $e) {
      $this->logger->error('Weather API error: @msg', [
        '@msg' => $e->getMessage(),
      ]);

      return ['error' => 'Weather data unavailable'];
    }
  }

  /**
   * Format API response into UI-friendly structure.
   */
  private function formatResponse(array $data): array {
    $timezoneOffset = $data['timezone'] ?? 0;

    return [
      'city' => $data['name'] ?? 'Unknown',
      'temp' => round($data['main']['temp'] ?? 0, 1),
      'condition' => ucfirst(strtolower($data['weather'][0]['main'] ?? '')),
      'icon' => $data['weather'][0]['icon'] ?? '',
      'humidity' => $data['main']['humidity'] ?? 0,
      'wind' => ($data['wind']['speed'] ?? 0) . ' m/s',
      'sunrise' => gmdate('H:i', ($data['sys']['sunrise'] ?? 0) + $timezoneOffset),
      'sunset' => gmdate('H:i', ($data['sys']['sunset'] ?? 0) + $timezoneOffset),
      'updated' => gmdate('H:i', ($data['dt'] ?? time()) + $timezoneOffset),
    ];
  }

  /**
   * Fetch 7-day forecast using One Call API.
   */
  public function getWeeklyForecast($lat, $lon, $unit = 'metric') {
    $apiKey = $this->settings->get('weather_api_key');

    try {
      $response = $this->httpClient->request(
        'GET',
        'https://api.openweathermap.org/data/2.5/forecast',
        [
          'query' => [
            'lat' => $lat,
            'lon' => $lon,
            'appid' => $apiKey,
            'units' => $unit,
          ],
        ]
      );

      $data = json_decode($response->getBody(), TRUE);

      return $this->formatForecast($data['list'] ?? []);
    }
    catch (\Exception $e) {
      \Drupal::logger('weather_widget')->error($e->getMessage());
      return [];
    }
  }

  /**
   * Format 7-day forecast.
   */
  private function formatForecast(array $list) {
    $days = [];

    foreach ($list as $item) {
      $date = date('Y-m-d', $item['dt']);
      if (!isset($days[$date]) && strpos($item['dt_txt'], '12:00:00')) {
        $days[$date] = [
          'day' => date('D', $item['dt']),
          'temp' => round($item['main']['temp']),
          'icon' => $item['weather'][0]['icon'],
        ];
      }

      if (count($days) >= 7) {
        break;
      }
    }

    return array_values($days);
  }

  /**
   *
   */
  public function getCoordsFromCity($city) {
    $apiKey = $this->settings->get('weather_api_key');

    try {
      $response = $this->httpClient->request('GET',
        'http://api.openweathermap.org/geo/1.0/direct',
        [
          'query' => [
            'q' => $city,
            'limit' => 1,
            'appid' => $apiKey,
          ],
        ]
      );

      $data = json_decode($response->getBody(), TRUE);

      if (!empty($data[0])) {
        return [
          'lat' => $data[0]['lat'],
          'lon' => $data[0]['lon'],
        ];
      }

      return NULL;
    }
    catch (\Exception $e) {
      return NULL;
    }
  }

}
