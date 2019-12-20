<?php

namespace Drupal\hcpss_schoolsite_config\Plugin\ConfigFilter;

use Drupal\config_filter\Plugin\ConfigFilterBase;

/**
 * @ConfigFilter(
 *   id = "school_config_filter",
 *   label = @Translation("School Filter"),
 *   weight = 0,
 *   status = TRUE,
 *   storages = {"config.storage.sync"},
 * )
 */
class SchoolConfigFilter extends ConfigFilterBase {
  public function filterRead($name, $delta) {
    //echo "$name\n";
  }
}
