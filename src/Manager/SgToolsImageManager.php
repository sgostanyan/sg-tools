<?php


namespace Drupal\sg_tools\Manager;

use Drupal\Core\Entity\EntityTypeManager;

/**
 * Class SgToolsImageManager.
 *
 * @package Drupal\sg_tools\Manager
 */
class SgToolsImageManager {

  protected $entityTypeManager;

  /**
   * SgToolsFileManager constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManager $entityTypeManager
   *   Entity type manager.
   *   Render.
   */
  public function __construct(
    EntityTypeManager $entityTypeManager
  ) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * Get image styles.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getImageStyles() {
    $styles = $this->entityTypeManager->getStorage('image_style')
      ->loadMultiple();
    $styleList = [];
    foreach ($styles as $style) {
      $styleList[$style->id()] = $style->label();
    }
    return $styleList;
  }

}
