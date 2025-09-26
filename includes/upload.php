<?php
if (!defined('ABSPATH')) {
    exit;
}

add_action('rest_api_init', function () {
    register_rest_route('stickers/v1', '/upload', [
        'methods'  => 'POST',
        'callback' => 'stickers_upload_image',
        'permission_callback' => function () {
            return current_user_can('upload_files');
        },
    ]);
});

function stickers_upload_image(WP_REST_Request $request) {
    if (empty($_FILES['file'])) {
        return new WP_Error('no_file', 'Nenhum arquivo enviado', ['status' => 400]);
    }

    $file = $_FILES['file'];

    $upload_dir = wp_upload_dir();
    $reels_dir  = $upload_dir['basedir'] . '/stickers';
    if (!file_exists($reels_dir)) {
        wp_mkdir_p($reels_dir);
    }

    $file_path = $reels_dir . '/' . basename($file['name']);
    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        return [
            'success' => true,
            'url' => $upload_dir['baseurl'] . '/stickers/' . basename($file['name']),
        ];
    }

    return new WP_Error('upload_error', 'Falha ao mover o arquivo', ['status' => 500]);
}
