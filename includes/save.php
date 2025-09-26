<?php
if (!defined('ABSPATH')) exit;

add_action('rest_api_init', function () {
    register_rest_route('stickers/v1', '/save', [
        'methods'  => 'POST',
        'callback' => 'stickers_save_data',
        'permission_callback' => function () {
            return current_user_can('edit_posts');
        },
    ]);
});

function stickers_save_data(WP_REST_Request $request) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reels';

    $data = $request->get_json_params();

    $required = ['filename', 'type', 'category', 'subcategory', 'title', 'description', 'slug', 'tags'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            return new WP_Error('missing_field', "O campo {$field} é obrigatório", ['status' => 400]);
        }
    }

    $id          = isset($data['id']) ? intval($data['id']) : null;
    $filename    = sanitize_text_field($data['filename']);
    $type        = sanitize_text_field($data['type']);
    $category    = sanitize_text_field($data['category']);
    $subcategory = sanitize_text_field($data['subcategory']);
    $title       = sanitize_text_field($data['title']);
    $description = isset($data['description']) ? sanitize_textarea_field($data['description']) : '';
    $slug        = sanitize_title($data['slug']); 
    $tags        = isset($data['tags']) ? sanitize_textarea_field($data['tags']) : '';

    if ($id) {
        $updated = $wpdb->update(
            $table_name,
            [
                'filename'    => $filename,
                'type'        => $type,
                'category'    => $category,
                'subcategory' => $subcategory,
                'title'       => $title,
                'description' => $description,
                'slug'        => $slug,
                'tags'        => $tags,
            ],
            ['id' => $id],
            ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'],
            ['%d']
        );

        if ($updated === false) {
            return new WP_Error('db_error', 'Falha ao atualizar o registro', ['status' => 500]);
        }

        return ['success' => true, 'message' => 'Registro atualizado', 'id' => $id];
    } else {
        $inserted = $wpdb->insert(
            $table_name,
            [
                'filename'    => $filename,
                'type'        => $type,
                'category'    => $category,
                'subcategory' => $subcategory,
                'title'       => $title,
                'description' => $description,
                'slug'        => $slug,
                'tags'        => $tags,
            ],
            ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s']
        );

        if (!$inserted) {
            return new WP_Error('db_error', 'Falha ao inserir o registro', ['status' => 500]);
        }

        return ['success' => true, 'message' => 'Registro criado', 'id' => $wpdb->insert_id];
    }
}
