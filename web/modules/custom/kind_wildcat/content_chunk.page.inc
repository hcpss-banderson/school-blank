<?php

/**
 * @file
 * Contains content_chunk.page.inc.
 *
 * Page callback for Content chunk entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Content chunk templates.
 *
 * Default template: content_chunk.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_content_chunk(array &$variables) {
  // Fetch ContentChunk Entity Object.
  $content_chunk = $variables['elements']['#content_chunk'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
