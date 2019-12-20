<?php

namespace Drupal\kind_wildcat;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;
use Drupal\kind_wildcat\Entity\ContentChunkType;

/**
 * Defines a class to build a listing of Content chunk entities.
 *
 * @ingroup kind_wildcat
 */
class ContentChunkListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['name'] = $this->t('Name');
    
    $header['type'] = [
      'data' => $this->t('Type'),
      'class' => [RESPONSIVE_PRIORITY_MEDIUM],
    ];
    
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\kind_wildcat\Entity\ContentChunk $entity */
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.content_chunk.edit_form',
      ['content_chunk' => $entity->id()]
    );
    
    $type = ContentChunkType::load($entity->bundle());
    $row['type'] = $type->label();
    
    return $row + parent::buildRow($entity);
  }
}
