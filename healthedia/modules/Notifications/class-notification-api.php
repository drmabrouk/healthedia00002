<?php
class Healthedia_Notification_API {
    public function register_routes() {
        register_rest_route('healthedia/v1', '/notifications', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_notifications'),
            'permission_callback' => 'is_user_logged_in'
        ));
        register_rest_route('healthedia/v1', '/notifications/mark-read', array(
            'methods' => 'POST',
            'callback' => array($this, 'mark_all_read'),
            'permission_callback' => 'is_user_logged_in'
        ));
    }

    public function get_notifications() {
        $user_id = get_current_user_id();
        $posts = get_posts(array(
            'post_type' => 'healthedia_notif',
            'author' => $user_id,
            'posts_per_page' => 20,
            'post_status' => 'publish'
        ));

        $data = array();
        foreach ($posts as $post) {
            $data[] = array(
                'id' => $post->ID,
                'message' => $post->post_title,
                'link' => get_post_meta($post->ID, '_healthedia_notif_link', true),
                'is_read' => get_post_meta($post->ID, '_healthedia_notif_read', true) === '1',
                'date' => get_the_date('Y-m-d H:i:s', $post->ID)
            );
        }
        return rest_ensure_response($data);
    }

    public function mark_all_read() {
        $user_id = get_current_user_id();
        $posts = get_posts(array(
            'post_type' => 'healthedia_notif',
            'author' => $user_id,
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => '_healthedia_notif_read',
                    'compare' => 'NOT EXISTS'
                ),
                array(
                    'key' => '_healthedia_notif_read',
                    'value' => '0',
                    'compare' => '='
                )
            )
        ));

        foreach ($posts as $post) {
            update_post_meta($post->ID, '_healthedia_notif_read', '1');
        }

        return rest_ensure_response(array('success' => true));
    }

    public static function add_notification($user_id, $message, $link = '') {
        $post_id = wp_insert_post(array(
            'post_type' => 'healthedia_notif',
            'post_title' => sanitize_text_field($message),
            'post_status' => 'publish',
            'post_author' => $user_id
        ));

        if (!is_wp_error($post_id)) {
            update_post_meta($post_id, '_healthedia_notif_read', '0');
            if ($link) {
                update_post_meta($post_id, '_healthedia_notif_link', esc_url_raw($link));
            }
        }
    }
}
