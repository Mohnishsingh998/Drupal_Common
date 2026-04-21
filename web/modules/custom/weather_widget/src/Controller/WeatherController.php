<?php

namespace Drupal\weather_widget\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\weather_widget\Service\WeatherService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;

class WeatherController implements ContainerInjectionInterface {

  protected WeatherService $weatherService;

  public function __construct(WeatherService $weather_service) {
    $this->weatherService = $weather_service;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('weather_widget.weather_service')
    );
  }

  public function getLocationWeather(Request $request) {
    $lat = $request->query->get('lat');
    $lon = $request->query->get('lon');

    if (!$lat || !$lon) {
      return new JsonResponse(['error' => 'Missing coordinates'], 400);
    }

    $data = $this->weatherService->getWeatherByCoords((float) $lat, (float) $lon);

    return new JsonResponse($data);
  }

}