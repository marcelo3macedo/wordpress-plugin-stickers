<?php
/**
 * Plugin Name:       Stickers
 * Plugin URI:        https://professoraantenada.com.br/figurinhas
 * Description:       Exibe as figurinhas disponíveis na plataforma.
 * Version:           1.0.0
 * Author:            Marcelo Macedo
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'includes/activation.php';
require_once plugin_dir_path(__FILE__) . 'includes/rewrite.php';
require_once plugin_dir_path(__FILE__) . 'includes/shortcode.php';
require_once plugin_dir_path(__FILE__) . 'includes/seo.php';
require_once plugin_dir_path(__FILE__) . 'includes/assets.php';
require_once plugin_dir_path(__FILE__) . 'includes/upload.php';
require_once plugin_dir_path(__FILE__) . 'includes/save.php';

register_activation_hook(__FILE__, 'stickers_activate_plugin');
register_deactivation_hook(__FILE__, 'stickers_deactivate_plugin');