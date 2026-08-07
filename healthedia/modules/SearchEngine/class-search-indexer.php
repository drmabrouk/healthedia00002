<?php
class Healthedia_Search_Indexer {
	public function index_post( $post_id, $post, $update ) {
		if ( wp_is_post_revision( $post_id ) || $post->post_status !== 'publish' ) {
			return;
		}

		global $wpdb;
		$table = $wpdb->prefix . 'healthedia_search_index';

		$data = array(
			'object_id'   => $post_id,
			'object_type' => $post->post_type,
			'title'       => $post->post_title,
			'content'     => wp_strip_all_tags( $post->post_content ),
			'metadata'    => json_encode( get_post_meta( $post_id ) ),
			'indexed_at'  => current_time('mysql')
		);

		$existing = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $table WHERE object_id = %d AND object_type = %s", $post_id, $post->post_type ) );

		if ( $existing ) {
			$wpdb->update( $table, $data, array( 'id' => $existing ) );
		} else {
			$wpdb->insert( $table, $data );
		}
	}

	public function index_user( $user_id, $old_user_data = null ) {
		$user = get_userdata( $user_id );
		if ( ! $user ) return;

		global $wpdb;
		$table = $wpdb->prefix . 'healthedia_search_index';

		$title = $user->first_name . ' ' . $user->last_name;
		if ( empty( trim( $title ) ) ) {
			$title = $user->display_name;
		}

		$data = array(
			'object_id'   => $user_id,
			'object_type' => 'user',
			'title'       => $title,
			'content'     => get_user_meta( $user_id, 'description', true ),
			'metadata'    => json_encode( get_user_meta( $user_id ) ),
			'indexed_at'  => current_time('mysql')
		);

		$existing = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $table WHERE object_id = %d AND object_type = %s", $user_id, 'user' ) );

		if ( $existing ) {
			$wpdb->update( $table, $data, array( 'id' => $existing ) );
		} else {
			$wpdb->insert( $table, $data );
		}
	}
}
