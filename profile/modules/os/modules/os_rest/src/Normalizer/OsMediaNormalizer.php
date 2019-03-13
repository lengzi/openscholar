<?php

namespace Drupal\os_rest\Normalizer;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\serialization\Normalizer\ContentEntityNormalizer;

/**
 * Converts Media to more usable format.
 *
 * @package Drupal\os_rest\Normalizer
 */
class OsMediaNormalizer extends ContentEntityNormalizer {

  /**
   * Entity Type Manager interface.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Local file system.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * The interface or class that this Normalizer supports.
   *
   * @var string
   */
  protected $supportedInterfaceOrClass = 'Drupal\media\MediaInterface';

  /**
   * OsMediaNormalizer constructor.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   Deprecated, from parent class.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   Entity Type Manager.
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   Current Route match.
   * @param \Drupal\Core\File\FileSystemInterface $fileSystem
   *   The local file system manager.
   */
  public function __construct(EntityManagerInterface $entity_manager, EntityTypeManagerInterface $entityTypeManager, RouteMatchInterface $routeMatch, FileSystemInterface $fileSystem) {
    parent::__construct($entity_manager);
    $this->entityTypeManager = $entityTypeManager;
    $this->routeMatch = $routeMatch;
    $this->fileSystem = $fileSystem;
  }

  /**
   * {@inheritdoc}
   */
  public function normalize($object, $format = NULL, array $context = []) {
    /** @var \Drupal\media\MediaInterface $media */
    // Just to get type hinting.
    $media = $object;
    $temp = parent::normalize($object, $format, $context);

    $output['mid'] = $media->id();
    $output['name'] = $media->getName();
    $output['created'] = $media->getCreatedTime();
    $output['changed'] = $temp['changed'][0]['value'];
    $output['type'] = $media->bundle();
    $source = $media->getSource();
    $file = $source->getSourceFieldValue($media);
    if (is_numeric($file)) {
      /** @var \Drupal\file\FileInterface $file */
      $file = $this->entityTypeManager->getStorage('file')->load($file);
      $output['fid'] = $file->id();
      $output['url'] = file_create_url($file->getFileUri());
      $output['size'] = $file->getSize();
      $output['filename'] = $file->getFilename();
      $output['schema'] = $this->fileSystem->uriScheme($file->getFileUri());

      if (!empty($temp['field_media_image'])) {
        $output['alt'] = $temp['field_media_image'][0]['alt'];
        $output['title'] = $temp['field_media_image'][0]['title'];
      }
    }
    else {
      $output['filename'] = $file;
    }
    $output['description'] = !empty($temp['field_description']) ? $temp['field_description'][0]['value'] : '';
    $output['thumbnail'] = $temp['thumbnail'][0]['url'];

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function denormalize($data, $class, $format = NULL, array $context = []) {
    $entity = parent::denormalize($data, $class, $format, $context);

    if (in_array('alt', $entity->_restSubmittedFields) || in_array('title', $entity->_restSubmittedFields)) {
      $entity->_restSubmittedFields = array_diff($entity->_restSubmittedFields, ['alt', 'title']);
      $entity->_restSubmittedFields[] = 'field_media_image';
    }
    $entity->restSubmittedFields = array_diff($entity->_restSubmittedFields, ['fid']);

    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function denormalizeFieldData(array $data, FieldableEntityInterface $entity, $format, array $context) {
    $original = $this->routeMatch->getParameter('media');

    if (!empty($data['name'])) {
      $input = [
        'name' => [
          'value' => $data['name'],
        ],
      ];
      $fieldList = $entity->get('name');
      $fieldList->setValue([]);
      $class = get_class($fieldList);
      $context['target_instance'] = $fieldList;
      $this->serializer->deserialize(json_encode($input), $class, $format, $context);
    }
    if (!empty($data['changed'])) {
      $input = [
        'changed' => [
          'value' => $data['changed'],
        ],
      ];
      $fieldList = $entity->get('changed');
      $fieldList->setValue([]);
      $class = get_class($fieldList);
      $context['target_instance'] = $fieldList;
      $this->serializer->deserialize(json_encode($input), $class, $format, $context);
    }
    if (!empty($data['alt']) || !empty($data['title'])) {
      $input = $original->get('field_media_image')->get(0)->getValue();

      if (!empty($data['alt'])) {
        $input['alt'] = $data['alt'];
      }
      if (!empty($data['title'])) {
        $input['title'] = $data['title'];
      }

      $fieldList = $entity->get('field_media_image');
      $class = get_class($fieldList);
      $context['target_instance'] = $fieldList;
      $this->serializer->deserialize(json_encode([$input]), $class, $format, $context);
    }
    if (!empty($data['description'])) {
      $input = [
        'field_description' => $data['description'],
      ];

      $fieldList = $entity->get('name');
      $fieldList->setValue([]);
      $class = get_class($fieldList);
      $context['target_instance'] = $fieldList;
      $this->serializer->deserialize($input, $class, $format, $context);
    }
  }

}