<?php
if (!defined('ABSPATH')) exit;

add_action('wp_enqueue_scripts', 'meu_stickers_enqueue_styles');
function meu_stickers_enqueue_styles() {
    wp_enqueue_style('meu-stickers-tailwind', 'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css', [], '2.2.19');
}
