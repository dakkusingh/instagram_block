<?php

/**
 * @file
 * Hooks provided by the Instagram Block API.
 */

/**
 * Allow modules to alter block settings.
 *
 * @param string $delta
 *   Block delta. Either user block or tag block.
 * @param array $settings
 *   Block settings.
 * @param array $config
 *   Global Instagram config.
 *   @see instagram_block_admin_settings variable.
 *
 * @see instagram_block_block_view().
 */
function hook_instagram_block_settings_alter($delta, &$settings, &$config) {
  if ($delta == 'instagram_block_tag') {
    $settings['tag'] = 'Instagram';
  }
}
