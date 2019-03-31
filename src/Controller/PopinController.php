<?php

namespace Drupal\sg_tools\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\sg_tools\Manager\SgToolsEntityRenderManager;
use Drupal\sg_tools\Manager\SgToolsEntityStorageManager;

/**
 * Class PopinController.
 */
class PopinController extends ControllerBase {

  /**
   * EntityStorage.
   *
   * @var \Drupal\sg_tools\Manager\SgToolsEntityStorageManager
   */
  protected $sgToolsEntityStorageManager;

  /**
   * EntityRender.
   *
   * @var \Drupal\sg_tools\Manager\SgToolsEntityRenderManager
   */
  protected $sgToolsEntityRenderManager;

  /**
   * Constructs a new PopinController object.
   *
   * @param \Drupal\sg_tools\Manager\SgToolsEntityStorageManager
   *   EntityStorageManager.
   * @param \Drupal\sg_tools\Manager\SgToolsEntityRenderManager $sgToolsEntityRenderManager
   */
  public function __construct(
    SgToolsEntityStorageManager $sgToolsEntityStorageManager,
    SgToolsEntityRenderManager $sgToolsEntityRenderManager
  ) {
    $this->sgToolsEntityStorageManager = $sgToolsEntityStorageManager;
    $this->sgToolsEntityRenderManager = $sgToolsEntityRenderManager;
  }

  /**
   * Get popin.
   *
   * @param string $entityType
   *   Entity type.
   * @param string $id
   *   Id.
   * @param mixed $viewMode
   *   View mode.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   Render array.
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getPopin($entityType, $id, $viewMode = 'default') {
    $entities = ($entityType && $id) ? $this->sgToolsEntityStorageManager->getEntitiesById([$id],
      $entityType) : NULL;
    return $entities ? $this->sgToolsEntityRenderManager->renderEntity(reset($entities),
      $viewMode) : NULL;
  }

}
