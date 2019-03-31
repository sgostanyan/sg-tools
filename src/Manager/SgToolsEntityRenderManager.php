<?php


namespace Drupal\sg_tools\Manager;

use Drupal\Core\Entity\Entity;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Render\Renderer;

/**
 * Class SgToolsEntityRenderManager.
 *
 * @package Drupal\sg_tools\Manager
 */
class SgToolsEntityRenderManager {

  protected $entityTypeManager;

  /**
   * Render.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * SgToolsEntityRenderManager constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManager $entityTypeManager
   *   Entity type manager.
   * @param \Drupal\Core\Render\Renderer $renderer
   *   Render.
   */
  public function __construct(
    EntityTypeManager $entityTypeManager,
    Renderer $renderer
  ) {
    $this->entityTypeManager = $entityTypeManager;
    $this->renderer = $renderer;
  }

  /**
   * Render entity.
   *
   * @param \Drupal\Core\Entity\Entity $entity
   *   Entity.
   * @param string $viewMode
   *   View mode.
   *
   * @return mixed
   *   Renderable array.
   */
  public function renderEntity($entity, $viewMode = NULL) {
    return $this->entityTypeManager->getViewBuilder($entity->getEntityType()->id())
      ->view($entity, $viewMode);
  }

  /**
   * @param array $view
   *
   * @return \Drupal\Component\Render\MarkupInterface|string
   * @throws \Exception
   */
  public function htmlRenderEntity(array $view) {
    return $this->renderer->render($view);
  }

}
