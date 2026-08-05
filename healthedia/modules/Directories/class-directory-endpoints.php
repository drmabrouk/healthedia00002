<?php
class Healthedia_Directory_Endpoints {
	public function register_routes() {
		register_rest_route('healthedia/v1', '/directories/researchers', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_researchers'),
			'permission_callback' => '__return_true'
		));
	}

	public function get_researchers($request) {
		$page = $request->get_param('page') ?: 1;
		$per_page = 20;
		$specialty = sanitize_text_field($request->get_param('specialty'));

		$args = array(
			'role__in' => array('member', 'researcher', 'reviewer', 'editor', 'administrator'),
			'orderby' => 'display_name',
			'order'   => 'ASC',
			'number'  => $per_page,
			'paged'   => $page,
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'relation' => 'OR',
					array(
						'key'     => '_healthedia_privacy_mode',
						'compare' => 'NOT EXISTS',
					),
					array(
						'key'     => '_healthedia_privacy_mode',
						'value'   => 'public',
						'compare' => '=',
					),
				)
			)
		);

		if (!empty($specialty)) {
			$args['meta_query'][] = array(
				'key' => '_healthedia_specialty',
				'value' => $specialty,
				'compare' => '='
			);
		}

		$user_query = new WP_User_Query( $args );
		$users = $user_query->get_results();

		$data = array();
		foreach ($users as $user) {
			require_once HEALTHEDIA_PLUGIN_DIR . 'modules/Profiles/class-profile-model.php';
			require_once HEALTHEDIA_PLUGIN_DIR . 'modules/Profiles/class-profile-verification.php';
			$metrics = Healthedia_Profile_Model::get_metrics($user->ID);
			$username = get_user_meta($user->ID, '_healthedia_username', true);

			$data[] = array(
				'id' => $user->ID,
				'name' => $user->display_name,
				'specialty' => get_user_meta($user->ID, '_healthedia_specialty', true),
				'verified' => Healthedia_Profile_Verification::is_verified($user->ID),
				'views' => $metrics->views,
				'url' => Healthedia_Profile_Model::get_profile_url($user->ID)
			);
		}

		return rest_ensure_response(array(
			'data' => $data,
			'total' => $user_query->get_total()
		));
	}
}
