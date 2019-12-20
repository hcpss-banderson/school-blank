<?php

namespace Drupal\kind_wildcat\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Content chunk entities.
 *
 * @ingroup kind_wildcat
 */
interface ContentChunkInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Content chunk name.
   *
   * @return string
   *   Name of the Content chunk.
   */
  public function getName();

  /**
   * Sets the Content chunk name.
   *
   * @param string $name
   *   The Content chunk name.
   *
   * @return \Drupal\kind_wildcat\Entity\ContentChunkInterface
   *   The called Content chunk entity.
   */
  public function setName($name);

  /**
   * Gets the Content chunk creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Content chunk.
   */
  public function getCreatedTime();

  /**
   * Sets the Content chunk creation timestamp.
   *
   * @param int $timestamp
   *   The Content chunk creation timestamp.
   *
   * @return \Drupal\kind_wildcat\Entity\ContentChunkInterface
   *   The called Content chunk entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Content chunk revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Content chunk revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\kind_wildcat\Entity\ContentChunkInterface
   *   The called Content chunk entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Content chunk revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Content chunk revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\kind_wildcat\Entity\ContentChunkInterface
   *   The called Content chunk entity.
   */
  public function setRevisionUserId($uid);

}
