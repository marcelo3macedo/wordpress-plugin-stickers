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
    $is_single_stickers = (count($parts) > 1 && $parts[0] === 'figurinhas' && $slug !== 'figurinhas' && $parts[1] !== 'page');
    
    if ($is_single_stickers) {
        $reel = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE slug = %s", $slug));
        
        if ($reel) {
            meu_stickers_update_seo($reel);

            $img_url = esc_url("/wp-content/uploads/stickers/" . $reel->filename);

            $output = '<a href="/figurinhas" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 text-gray-800 text-md p-4">Voltar</a>';
            $output .= '<div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden md:max-w-2xl">';
            $output .= '<img src="' . $img_url . '" alt="' . esc_attr($reel->title) . '" class="w-full h-full object-cover">';
            $output .= '<div class="p-8">';
            $output .= '<div class="uppercase tracking-wide text-sm text-indigo-500 font-semibold">' . esc_html($reel->title) . '</div>';
            $output .= '<p class="mt-2 text-gray-500">' . nl2br(esc_html($reel->description)) . '</p>';

            $output .= '<div class="mt-6 flex space-x-4">';
            $output .= '<a href="' . $img_url . '" download class="px-4 w-full text-center py-2 rounded bg-purple-200 hover:bg-purple-400 text-purple-600 font-bold">Baixar</a>';
            $output .= '</div>';

            $output .= '</div>';
            $output .= '</div>';
        } else {
            $output = '<p class="text-center text-gray-600">Figurinhas não encontradas.</p>';
        }

    } else {
        $page = $parts[2] ?? "1";
        $per_page = 12;
        $offset = ($page - 1) * $per_page;

        $total_stickers = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        $stickers = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY id DESC LIMIT %d OFFSET %d", $per_page, $offset));
        
        $output .= '<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">';
        
        if ($stickers) {
            foreach ($stickers as $reel) {
                $img_url = esc_url("/wp-content/uploads/stickers/" . $reel->filename);

                $output .= '<div class="bg-white rounded-xl shadow-md overflow-hidden transform transition-transform hover:scale-105 flex flex-col">';
                $output .= '<a href="/figurinhas/' . esc_attr($reel->slug) . '" class="block flex-grow">';
                $output .= '<img src="' . $img_url . '" alt="' . esc_attr($reel->title) . '" class="w-full h-full object-cover">';
                $output .= '<div class="p-4">';
                $output .= '<div class="uppercase tracking-wide text-sm text-indigo-500 font-semibold truncate">' . esc_html($reel->title) . '</div>';
                $output .= '</div>';
                $output .= '</a>';

                $output .= '<div class="p-4">';
                $output .= '<a href="/figurinhas/' . esc_attr($reel->slug) . '" class="block text-center px-4 py-2 rounded bg-purple-200 hover:bg-purple-400 text-purple-600 text-sm font-bold">Visualizar</a>';
                $output .= '</div>';

                $output .= '</div>';
            }
        } else {
            $output .= '<p class="text-center text-gray-600 col-span-full">Nenhuma figurinha encontrada.</p>';
        }

        $output .= '</div>';

        $total_pages = ceil($total_stickers / $per_page);
        if ($total_pages > 1) {
            $current_url = get_permalink();
            
            $output .= '<div class="mt-8 flex justify-between">';
            
            if ($page > 1) {
                $prev_page_url = $page > 2 ? trailingslashit($current_url) . 'page/' . ($page - 1) : $current_url;
                $output .= '<a href="' . esc_url($prev_page_url) . '" class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">← Anterior</a>';
            } else {
                $output .= '<div></div>';
            }

            if ($page < $total_pages) {
                $next_page_url = trailingslashit($current_url) . 'page/' . ($page + 1);
                $output .= '<a href="' . esc_url($next_page_url) . '" class="px-4 py-2 rounded bg-indigo-500 text-white hover:bg-indigo-600">Próximo →</a>';
            } else {
                $output .= '<div></div>';
            }

            $output .= '</div>';
        }
    }

    return $output;
}
