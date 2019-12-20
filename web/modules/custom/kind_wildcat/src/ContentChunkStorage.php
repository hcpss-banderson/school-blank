<?php

namespace Drupal\kind_wildcat;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\kind_wildcat\Entity\ContentChunkInterface;

/**
 * Defines the storage handler class for Content chunk entities.
 *
 * This extends the base storage class, adding required special handling for
 * Content chunk entities.
 *
 * @ingroup kind_wildcat
 */
class ContentChunkStorage extends SqlContentEntityStorage implements ContentChunkStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(ContentChunkInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {content_chunk_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {content_chunk_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(ContentChunkInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {content_chunk_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('content_chunk_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
