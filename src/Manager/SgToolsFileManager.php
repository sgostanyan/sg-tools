<?php


namespace Drupal\sg_tools\Manager;

use Drupal\Core\Entity\EntityTypeManager;

/**
 * Class SgToolsFileManager.
 *
 * @package Drupal\sg_tools\Manager
 */
class SgToolsFileManager {

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
   * @param $fid
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getFileInfos($fid) {
    $file = $this->entityTypeManager->getStorage('file')->load($fid);
    if ($file) {
      $size = $this->sanitizeFileSize($file->getSize());
      $mimeArray = explode('/', $file->getMimeType());
      $fileMime = end($mimeArray);
      $url = $this->getFileUrl($fid);

      return [
        'mime' => $fileMime,
        'size' => $size,
        'url' => $url,
        'uri' => $file->getFileUri(),
      ];
    }
  }

  /**
   * @param $size
   *
   * @return int|string
   */
  public function sanitizeFileSize($size) {
    $fileSize = intval($size);
    $ko = $fileSize / 1000;
    if ($ko < 1000) {
      $fileSize = round($ko, 2) . " Ko";
    }
    else {
      $fileSize = round($ko / 1000, 2) . " Mo";
    }
    return $fileSize;
  }

  /**
   * Get file url.
   *
   * @param mixed $fid
   *   File id.
   *
   * @return string|null
   *   Url.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getFileUrl($fid) {
    if ($fid) {
      $file = $this->entityTypeManager->getStorage('file')->load($fid);
      if ($file) {
        $path = $file->getFileUri();
        if ($path) {
          $url = file_create_url($path);
          if ($url) {
            return $url;
          }
        }
      }
    }
    return NULL;
  }

}
