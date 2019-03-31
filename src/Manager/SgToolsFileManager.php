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
   * @param $source
   * @param $filename
   * @param null $destination
   *
   * @return int|string|null
   * @throws \Exception
   */
  public function generateFileEntity(
    $source,
    $filename,
    $destination = NULL
  ) {

    if (substr($source, -1) != "/") {
      $source = $source . "/";
    }
    if ($destination) {
      if (substr($destination, -1) != "/") {
        $destination = $destination . "/";
      }
    }
    else {
      $dateTime = new \DateTime();
      $destination = 'public://' . $dateTime->format('Y-m') . '/';
    }
    $filepath = $source . $filename;

    $data = file_get_contents($filepath);
    if ($data) {
      if (file_prepare_directory($destination, FILE_CREATE_DIRECTORY)) {
        $file = file_save_data($data, $destination . $filename,
          FILE_EXISTS_REPLACE);
        if ($file) {
          return $file->id();
        }
      }
    }
    return NULL;
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
