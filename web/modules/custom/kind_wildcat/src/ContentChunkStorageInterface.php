<?php

namespace Drupal\kind_wildcat;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface ContentChunkStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Content chunk revision IDs for a specific Content chunk.
   *
   * @param \Drupal\kind_wildcat\Entity\ContentChunkInterface $entity
   *   The Content chunk entity.
   *
   * @return int[]
   *   Content chunk revision IDs (in ascending order).
   */
  public function revisionIds(ContentChunkInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Content chunk author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Content chunk revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\kind_wildcat\Entity\ContentChunkInterface $entity
   *   The Content chunk entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(ContentChunkInterface $entity);

  /**
   * Unsets the language for all Content chunk with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
