<?php
if (!defined('ABSPATH')) exit;

function stickers_activate_plugin() {
    stickers_create_table();
    meu_stickers_rewrite_rules();
    #flush_rewrite_rules();
}

function stickers_deactivate_plugin() {
    #flush_rewrite_rules();
}

function stickers_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'stickers';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        filename varchar(8000) NOT NULL,
        type varchar(800),
        category varchar(800),
        subcategory varchar(800),
        title varchar(800),
        description text,
        slug varchar(800),
        tags text,
        PRIMARY KEY  (id),
        UNIQUE KEY slug (slug)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
