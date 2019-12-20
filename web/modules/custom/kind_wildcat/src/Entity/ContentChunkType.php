<?php

namespace Drupal\kind_wildcat\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Content chunk type entity.
 *
 * @ConfigEntityType(
 *   id = "content_chunk_type",
 *   label = @Translation("Content chunk type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\kind_wildcat\ContentChunkTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\kind_wildcat\Form\ContentChunkTypeForm",
 *       "edit" = "Drupal\kind_wildcat\Form\ContentChunkTypeForm",
 *       "delete" = "Drupal\kind_wildcat\Form\ContentChunkTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\kind_wildcat\ContentChunkTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "content_chunk_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "content_chunk",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/content_chunk_type/{content_chunk_type}",
 *     "add-form" = "/admin/structure/content_chunk_type/add",
 *     "edit-form" = "/admin/structure/content_chunk_type/{content_chunk_type}/edit",
 *     "delete-form" = "/admin/structure/content_chunk_type/{content_chunk_type}/delete",
 *     "collection" = "/admin/structure/content_chunk_type"
 *   }
 * )
 */
class ContentChunkType extends ConfigEntityBundleBase implements ContentChunkTypeInterface {

  /**
   * The Content chunk type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Content chunk type label.
   *
   * @var string
   */
  protected $label;

}
