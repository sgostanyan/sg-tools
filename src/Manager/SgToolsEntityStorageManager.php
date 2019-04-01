<?php


namespace Drupal\sg_tools\Manager;

use Drupal\Core\Entity\Entity;
use Drupal\Core\Entity\EntityRepository;
use Drupal\Core\Entity\EntityTypeManager;

/**
 * Class SgToolsEntityStorageManager.
 *
 * @package Drupal\sg_tools\Manager
 */
class SgToolsEntityStorageManager {

  protected $entityTypeManager;

  protected $entityRepository;

  /**
   * SgToolsEntityStorageManager constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManager $entityTypeManager
   *   Entity type manager.
   *   Render.
   * @param \Drupal\Core\Entity\EntityRepository $entityRepository
   */
  public function __construct(
    EntityTypeManager $entityTypeManager,
    EntityRepository $entityRepository
  ) {
    $this->entityTypeManager = $entityTypeManager;
    $this->entityRepository = $entityRepository;
  }

  /**
   * @param array $ids
   * @param string $entityType
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getEntitiesById(array $ids, string $entityType) {
    $entities = $this->entityTypeManager->getStorage($entityType)
      ->loadMultiple($ids);
    $entitiesArray = [];
    foreach ($entities as $key => $entity) {
      $entitiesArray[$key] = $this->entityRepository->getTranslationFromContext($entity);
    }
    return $entitiesArray;
  }

  /**
   * @param string $entityType
   * @param array $values
   *
   * @return \Drupal\Core\Entity\EntityInterface
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function createEntity(string $entityType, array $values) {
    $entity = $this->entityTypeManager->getStorage($entityType)
      ->create($values);
    $entity->save();
    return $entity;
  }

  /**
   * @param \Drupal\Core\Entity\Entity $entity
   * @param $lang
   * @param array $values
   *
   * @return mixed
   */
  public function addTranslation(Entity $entity, $lang, array $values) {
    if ($entity && !$entity->hasTranslation($lang)) {
      $entityArray = $entity->toArray();
      $translatedEntityArray = array_merge($entityArray, $values);
      $translatedEntity = $entity->addTranslation($lang,
        $translatedEntityArray);
      $translatedEntity->save();
      return $translatedEntity;
    }
    return NULL;
  }
}
