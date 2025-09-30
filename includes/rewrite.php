<?php
if (!defined('ABSPATH')) exit;

add_action('init', 'meu_stickers_rewrite_rules');
function meu_stickers_rewrite_rules() {
    add_rewrite_rule('^figurinhas/([a-zA-Z0-9_-]+)/?$', 'index.php?pagename=figurinhas&sticker_slug=$matches[1]', 'top');
}

add_filter('query_vars', function($vars) {
    $vars[] = 'sticker_slug';
    return $vars;
});
