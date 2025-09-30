<?php
if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'helpers/modal.php';

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
        $page     = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
        $per_page = 12;
        $offset   = ($page - 1) * $per_page;

        $where_clauses = [];
        $params = [];

        if (!empty($_GET['cabelo'])) {
            $cabelos = explode(',', sanitize_text_field($_GET['cabelo']));
            $placeholders = implode(',', array_fill(0, count($cabelos), '%s'));
            $where_clauses[] = "hair IN ($placeholders)";
            $params = array_merge($params, $cabelos);
        }

        if (!empty($_GET['olhos'])) {
            $olhos = explode(',', sanitize_text_field($_GET['olhos']));
            $placeholders = implode(',', array_fill(0, count($olhos), '%s'));
            $where_clauses[] = "eyes IN ($placeholders)";
            $params = array_merge($params, $olhos);
        }

        $where_sql = $where_clauses ? "WHERE " . implode(" AND ", $where_clauses) : "";

        $sql_total = "SELECT COUNT(*) FROM $table_name $where_sql";
        $total_stickers = $wpdb->get_var($wpdb->prepare($sql_total, $params));

        $sql_items = "SELECT * FROM $table_name $where_sql ORDER BY id DESC LIMIT %d OFFSET %d";
        $params_items = array_merge($params, [$per_page, $offset]);
        $stickers = $wpdb->get_results($wpdb->prepare($sql_items, $params_items));

        $output .= '<div class="text-center my-6">';
        $output .= '<button id="filter-btn" class="px-6 py-3 rounded-full bg-indigo-500 text-white font-semibold shadow-lg hover:bg-indigo-600 transition-colors duration-300 flex items-center justify-center mx-auto">';
        $output .= '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">';
        $output .= '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3c1.359 0 2.666.023 3.96.062 1.46.044 2.378.892 2.636 2.36l1.246 7.478a.862.862 0 0 1-.774.965l-1.42.237c-.754.126-1.508.196-2.26.216m-1.748-8.25a.862.862 0 0 1 .775-.965l1.42-.237c.754-.126 1.508-.196 2.26-.216m-1.748-8.25l-2.738 1.66c-.66.402-1.282.906-1.854 1.48L5.75 8.75m1.5-1.5a.75.75 0 0 0-.75.75v3.25c0 .414.336.75.75.75h9.5c.414 0 .75-.336.75-.75V8a.75.75 0 0 0-.75-.75h-9.5z" />';
        $output .= '</svg>';
        $output .= 'Filtros</button>';
        $output .= '</div>';
        
        $output .= '<div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">';
        
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

        $output = stickers_add_modal($output);

        $total_pages = ceil($total_stickers / $per_page);
        if ($total_pages > 1) {
            $current_url = home_url(add_query_arg([]));
            $query_args  = $_GET;

            $output .= '<div class="mt-8 flex justify-between">';

            if ($page > 1) {
                $query_args['pagina'] = $page - 1;
                $prev_page_url = add_query_arg($query_args, $current_url);
                $output .= '<a href="' . esc_url($prev_page_url) . '" class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">← Anterior</a>';
            } else {
                $output .= '<div></div>';
            }

            if ($page < $total_pages) {
                $query_args['pagina'] = $page + 1;
                $next_page_url = add_query_arg($query_args, $current_url);
                $output .= '<a href="' . esc_url($next_page_url) . '" class="px-4 py-2 rounded bg-indigo-500 text-white hover:bg-indigo-600">Próximo →</a>';
            } else {
                $output .= '<div></div>';
            }

            $output .= '</div>';
        }
    }

    $output = stickers_add_modal_script($output);

    return $output;
}
