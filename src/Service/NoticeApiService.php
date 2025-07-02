<?php

namespace Drupal\notices_data\Service;

use GuzzleHttp\ClientInterface;

/**
 * Service to fetch notices data from The Gazette REST API.
 */
class NoticeApiService {

   /**
   * Guzzle HTTP client for making API requests.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  // API URL of The Gazette notices JSON endpoint.
  const API_URL = 'https://www.thegazette.co.uk/all-notices/notice/data.json';

  /**
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The HTTP client used to perform requests.
   */
  public function __construct(ClientInterface $http_client) {
    $this->httpClient = $http_client;
  }

  public function getNotices($page = 1) {
    $options = [
      'query' => [
        'results-page' => $page,
      ]
      // commenting this as i didnt receive the 'self-signed' certificate error.
      //'verify' => FALSE,
    ];

    try {
      $response = $this->httpClient->request('GET', self::API_URL, $options);
      return json_decode($response->getBody(), TRUE);
    }
    catch (\Exception $e) {

      // Log the error to drupal logger.
      \Drupal::logger('notices_data')->error($e->getMessage());
      return NULL;
    }
  }
}
