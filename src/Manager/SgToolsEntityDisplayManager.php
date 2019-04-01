<?php

namespace Drupal\sg_tools\Manager;

use Drupal\Core\Entity\EntityDisplayRepository;

/**
 * Class SgToolsEntityDisplayManager.
 *
 * @package Drupal\sg_tools\Manager
 */
class SgToolsEntityDisplayManager {

  protected $entityDisplayRepository;

  /**
   * SgToolsEntityDisplayManager constructor.
   *
   * @param \Drupal\Core\Entity\EntityDisplayRepository $entityDisplayRepository
   */
  public function __construct(
    EntityDisplayRepository $entityDisplayRepository
  ) {
    $this->entityDisplayRepository = $entityDisplayRepository;
  }

  /**
   * GetViewModes.
   *
   * @param string $entityType
   *   EntityType.
   *
   * @return array
   *   View mode array.
   */
  public function getViewModes($entityType) {
    $viewModes = $this->entityDisplayRepository->getViewModes($entityType);
    $viewModesSanit = [];
    if ($viewModes) {
      foreach ($viewModes as $label => $viewMode) {
        $viewModesSanit[$label] = $viewMode['id'];
      }
    }
    return $viewModesSanit;
  }

}
