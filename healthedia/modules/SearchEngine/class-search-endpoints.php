<?php
class Healthedia_Search_Endpoints {
	public function register_routes() {
		register_rest_route('healthedia/v1', '/search', array(
			'methods' => 'GET',
			'callback' => array($this, 'do_search'),
			'permission_callback' => '__return_true'
		));
	}

	public function do_search($request) {
		global $wpdb;
		$query = sanitize_text_field($request->get_param('q'));
		$type = sanitize_text_field($request->get_param('type'));

		if (empty($query)) {
			return rest_ensure_response(array('results' => []));
		}

		$table = $wpdb->prefix . 'healthedia_search_index';

		$args = array($query . '*');
		$sql = "SELECT object_id, object_type, title, metadata FROM $table WHERE MATCH(title, content) AGAINST(%s IN BOOLEAN MODE)";

		if (!empty($type)) {
			$sql .= " AND object_type = %s";
			$args[] = $type;
		}

		$sql .= " LIMIT 20";

		$results = $wpdb->get_results($wpdb->prepare($sql, $args));

		$formatted_results = array();
		foreach ($results as $row) {
			// Skip private/hidden profiles
			if ($row->object_type === 'user') {
				$privacy_mode = get_user_meta($row->object_id, '_healthedia_privacy_mode', true) ?: 'public';
				if (in_array($privacy_mode, ['private', 'hidden'])) continue;
			}

			$raw_meta = json_decode($row->metadata, true);
			$safe_meta = array();

			$formatted_row = array(
				'id' => $row->object_id,
				'type' => $row->object_type,
				'title' => $row->title
			);

			// Filter to only expose explicit, safe public metadata
			if ($row->object_type === 'user') {
				if (isset($raw_meta['_healthedia_specialty'])) $safe_meta['specialty'] = $raw_meta['_healthedia_specialty'][0];
				if (isset($raw_meta['_healthedia_verified'])) $safe_meta['verified'] = $raw_meta['_healthedia_verified'][0];
				require_once HEALTHEDIA_PLUGIN_DIR . 'modules/Profiles/class-profile-model.php';
				$formatted_row['url'] = Healthedia_Profile_Model::get_profile_url($row->object_id);
			} else {
				if (isset($raw_meta['_healthedia_doi'])) $safe_meta['doi'] = $raw_meta['_healthedia_doi'][0];
				$formatted_row['url'] = get_permalink($row->object_id);
			}

			$formatted_row['meta'] = $safe_meta;
			$formatted_results[] = $formatted_row;
		}

		return rest_ensure_response($formatted_results);
	}
}
