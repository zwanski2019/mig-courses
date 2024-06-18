add_action('init', function() {
    if ( !defined('WP_CLI') ) {
        $new_password_hash = wp_hash_password('Place2be****');
        echo 'New Password Hash: ' . $new_password_hash;
        exit;
    }
});
