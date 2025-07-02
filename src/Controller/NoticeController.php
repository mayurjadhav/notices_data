<?php

namespace Drupal\notices_data\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Pager\PagerManagerInterface;
use Drupal\Core\Pager\PagerParameters;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\notices_data\Service\NoticeApiService;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Notice Controller for displaying Gazette notices with Drupal pager.
 */
class NoticeController extends ControllerBase {

  /** 
   * The page Manager
   * 
   * @var \Drupal\Core\Pager\PagerManagerInterface 
   */
  protected $pagerManager;

  /** 
   * 
   * The page Parameters
   * 
   * @var \Drupal\Core\Pager\PagerParameters 
   */
  protected $pagerParameters;

   /**
   * API service to fetch notice data.
   *
   * @var \Drupal\notices_data\Service\NoticeApiService
   */
  protected $apiService;

    /**
   * Construct the controller with injected services.
   *
   * @param \Drupal\Core\Pager\PagerManagerInterface $pagerManager
   *   Service to manage paging.
   * @param \Drupal\Core\Pager\PagerParameters $pagerParameters
   *   Service to extract page number.
   * @param \Drupal\notices_data\Service\NoticeApiService $apiService
   *   Service to fetch data from The Gazette API.
   */
  public function __construct(PagerManagerInterface $pagerManager, PagerParameters $pagerParameters, NoticeApiService $apiService) {
    $this->pagerManager = $pagerManager;
    $this->pagerParameters = $pagerParameters;
    $this->apiService = $apiService;
  }

   /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('pager.manager'),
      $container->get('pager.parameters'),
      $container->get('notices_data.api_service')
    );
  }

   /**
   * Builds the notices list page with pagination.
   */
  public function listNotices() {

    // Get the current page.
    $current_page = $this->pagerParameters->findPage();
    $page_number = $current_page + 1;

    $data = $this->apiService->getNotices($page_number);

    $notices = [];

    // Loop through each notices and build our renderable data.
    if (isset($data['entry']) && is_array($data['entry'])) {
      foreach ($data['entry'] as $notice) {

        $link_href = !empty($notice['link'][0]['@href']) ? $notice['link'][0]['@href'] : '';
        $link_rel = !empty($notice['link'][0]['@rel']) ? $notice['link'][0]['@rel'] : '';

        // Generate the renderable link.
        if ($link_href) {
          $link = Link::fromTextAndUrl($notice['title'], Url::fromUri($link_href, [
            'attributes' => ['rel' => $link_rel, 'target' => '_blank'],
          ]));
        }
        
        $notices[] = [
          'link' => $link ?? '#',
          'content' => $notice['content'] ?? '',
          'published' => !empty($notice['published']) ? date('j F Y', strtotime($notice['published'])) : '',
        ];
      }
    }

    // Get the total number of records.
    $total_items = $data['f:total'] ?? 0;
    if ($total_items > 0) {

      // Initialize Drupal pager.
      $this->pagerManager->createPager($data['f:total'], 10);
    }

    return [
      '#theme' => 'notices_list',
      '#notices' => $notices,
      '#pager' => [
        '#type' => 'pager',
      ],
      '#cache' => ['max-age' => 0],
    ];
  }
}
