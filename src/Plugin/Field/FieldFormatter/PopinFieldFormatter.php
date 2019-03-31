<?php

namespace Drupal\sg_tools\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityDisplayRepository;
use Drupal\Core\Entity\EntityRepository;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'popin_field_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "popin_field_formatter",
 *   label = @Translation("Popin"),
 *   field_types = {
 *     "image"
 *   }
 * )
 */
class PopinFieldFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\Core\Entity\EntityRepository
   */
  protected $entityRepository;

  /**
   * @var \Drupal\Core\Entity\EntityDisplayRepository
   */
  protected $entityDisplayRepository;

  /**
   * @var array
   */
  protected $styleList;

  /**
   * QueryStringFieldFormatter constructor.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Any third party settings settings.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager.
   * @param \Drupal\Core\Entity\EntityRepository $entity_repository
   *   Entity repository.
   * @param \Drupal\Core\Entity\EntityDisplayRepository $entityDisplayRepository
   *   EntityDisplayRepository.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    $label,
    $view_mode,
    array $third_party_settings,
    EntityTypeManagerInterface $entity_type_manager,
    EntityRepository $entity_repository,
    EntityDisplayRepository $entityDisplayRepository
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition,
      $settings, $label, $view_mode, $third_party_settings);
    $this->entityTypeManager = $entity_type_manager;
    $this->entityRepository = $entity_repository;
    $this->entityDisplayRepository = $entityDisplayRepository;

    /* Image styles */
    $styles = $this->entityTypeManager->getStorage('image_style')
      ->loadMultiple();
    $this->styleList = ['0' => 'No style'];
    foreach ($styles as $style) {
      $this->styleList[$style->id()] = $style->label();
    }
  }

  /**
   * Create.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   Container.
   * @param array $configuration
   *   Configuraion.
   * @param string $plugin_id
   *   Plugin id.
   * @param mixed $plugin_definition
   *   Plugin definition.
   *
   * @return \Drupal\afd_intranet_field_formatters\Plugin\Field\FieldFormatter\QueryStringFieldFormatter|\Drupal\Core\Plugin\ContainerFactoryPluginInterface
   *   Container factory.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition
  ) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('entity_type.manager'),
      $container->get('entity.repository'),
      $container->get('entity_display.repository')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        // Implement default settings.
        'width' => '640',
        'height' => '480',
        'view_mode' => '',
        'image_style' => '',
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {

    return [
      // Implement settings form.
      'width' => [
        '#title' => $this->t("Popin width"),
        '#type' => 'textfield',
        '#default_value' => $this->getSetting('width'),
      ],
      'height' => [
        '#title' => $this->t("Popin height"),
        '#type' => 'textfield',
        '#default_value' => $this->getSetting('height'),
      ],
      'view_mode' => [
        '#title' => $this->t('Popin entity view modes'),
        '#type' => 'select',
        '#options' => $this->getViewModes($this->fieldDefinition->getTargetEntityTypeId()),
        '#default_value' => $this->getSetting('view_mode'),
      ],
      'image_style' => [
          '#title' => $this->t("Style d'image"),
          '#type' => 'select',
          '#options' => $this->styleList,
          '#description' => $this->t("Style d'image à appliquer"),
          '#default_value' => $this->getSetting('image_style'),
        ] + parent::settingsForm($form, $form_state),
    ];
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
  protected function getViewModes($entityType) {
    $viewModes = $this->entityDisplayRepository->getViewModes($entityType);
    $viewModesSanit = [];
    if ($viewModes) {
      foreach ($viewModes as $label => $viewMode) {
        $viewModesSanit[$label] = $viewMode['id'];
      }
    }
    return $viewModesSanit;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    // Implement settings summary.
    $summary[] = t("Width") . " : " . $this->getSetting('width');
    $summary[] = t("Height") . " : " . $this->getSetting('height');
    $summary[] = t("View mode") . " : " . $this->getSetting('view_mode');
    $summary[] = t("Image style") . " : " . $this->styleList[$this->getSetting('image_style')];
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    $parentEntityType = $this->fieldDefinition->getTargetEntityTypeId();
    $width = $this->getSetting('width');
    $height = $this->getSetting('height');
    $viewMode = $this->getSetting('view_mode');
    $imageStyle = $this->getSetting('image_style');

    foreach ($items as $delta => $item) {

      $entity = $item->entity;
      $imageUrl = file_create_url($entity->getFileUri());
      if ($imageStyle) {
        $style = $this->entityTypeManager->getStorage('image_style')
          ->load($imageStyle);
        $imageUrl = $style->buildUrl($imageUrl);
      }
      $pid = $item->getParent()->getParent()->getValue()->id();
      if ($pid && $parentEntityType) {
        $elements[$delta] =
          [
            '#markup' => '<div class="popin"><a class="use-ajax" data-dialog-options="{&quot;width&quot;:' . $width . ',&quot;height&quot;:' . $height . '}" data-dialog-type="modal" href="/popin/render/' . $parentEntityType . '/' . $pid . '/' . $viewMode . '"><img src="' . $imageUrl . '"/></a></div>',
          ];
      }
    }

    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    // The text value has no text format assigned to it, so the user input
    // should equal the output, including newlines.
    return nl2br(Html::escape($item->value));
  }

}
