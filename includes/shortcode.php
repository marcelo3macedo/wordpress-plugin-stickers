<?php
if (!defined('ABSPATH')) exit;

add_shortcode('stickers', 'stickers_shortcode');

function stickers_shortcode() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'stickers';
    $output = '';

    $path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $parts = explode('/', $path);
    $slug = end($parts);
    $is_single_reels = (count($parts) > 1 && $parts[0] === 'stickers' && $slug !== 'stickers');

    if ($is_single_reels) {
        $reel = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE slug = %s", $slug));
        
        if ($reel) {
            meu_stickers_update_seo($reel);

            $img_url = esc_url("/wp-content/uploads/stickers/" . $reel->filename);

            $output = '<div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden md:max-w-2xl">';
            $output .= '<img src="' . $img_url . '" alt="' . esc_attr($reel->title) . '" class="w-full h-full object-cover">';
            $output .= '<div class="p-8">';
            $output .= '<div class="uppercase tracking-wide text-sm text-indigo-500 font-semibold">' . esc_html($reel->title) . '</div>';
            $output .= '<p class="mt-2 text-gray-500">' . nl2br(esc_html($reel->description)) . '</p>';
            $output .= '</div>';
            $output .= '</div>';
        } else {
            $output = '<p class="text-center text-gray-600">Reel não encontrado.</p>';
        }

    } else {
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $per_page = 12;
        $offset = ($page - 1) * $per_page;

        $total_reels = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 1");
        $reels = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY id DESC LIMIT %d OFFSET %d", $per_page, $offset));
        
        $output .= '<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">';
        
        if ($reels) {
            foreach ($reels as $reel) {
                $img_url = esc_url("/wp-content/uploads/stickers/" . $reel->filename);

                $output .= '<a href="/stickers/' . esc_attr($reel->slug) . '" class="block">';
                $output .= '<div class="bg-white rounded-xl shadow-md overflow-hidden transform transition-transform hover:scale-105">';
                $output .= '<img src="' . $img_url . '" alt="' . esc_attr($reel->title) . '" class="w-full h-full object-cover">';
                $output .= '<div class="p-4">';
                $output .= '<div class="uppercase tracking-wide text-sm text-indigo-500 font-semibold truncate">' . esc_html($reel->title) . '</div>';
                $output .= '</div>';
                $output .= '</div>';
                $output .= '</a>';
            }
        } else {
            $output .= '<p class="text-center text-gray-600 col-span-full">Nenhum Reel encontrado.</p>';
        }

        $output .= '</div>';

        $total_pages = ceil($total_reels / $per_page);
        if ($total_pages > 1) {
            $output .= '<div class="mt-8 flex justify-center space-x-2">';
            for ($i = 1; $i <= $total_pages; $i++) {
                $current_class = ($page === $i) ? 'bg-indigo-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300';
                $output .= '<a href="?page=' . $i . '" class="px-4 py-2 rounded ' . $current_class . '">' . $i . '</a>';
            }
            $output .= '</div>';
        }
    }

    return $output;
}
