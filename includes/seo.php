<?php
if (!defined('ABSPATH')) exit;

function meu_stickers_update_seo($reel) {
    if (!$reel || !is_singular('page')) {
        return;
    }
    
    add_action('wp_head', function() use ($reel) {
        $title = esc_html($reel->title) . ' | Reels';
        $description = esc_html($reel->description);
        $tags = esc_html($reel->tags);

        echo '<meta name="description" content="' . $description . '" />' . "\n";
        echo '<meta name="keywords" content="' . $tags . '" />' . "\n";
        
        echo '<meta property="og:title" content="' . $title . '" />' . "\n";
        echo '<meta property="og:description" content="' . $description . '" />' . "\n";
        echo '<meta property="og:type" content="article" />' . "\n";
        echo '<meta property="og:url" content="' . get_permalink() . '" />' . "\n";
        
        echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
        echo '<meta name="twitter:title" content="' . $title . '" />' . "\n";
        echo '<meta name="twitter:description" content="' . $description . '" />' . "\n";
    });

    add_filter('pre_get_document_title', function($title) use ($reel) {
        return esc_html($reel->title) . ' | Reels';
    });
}
