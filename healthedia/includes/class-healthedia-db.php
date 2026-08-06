<?php
class Healthedia_DB {
	public static function create_tables() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$table_search_index = $wpdb->prefix . 'healthedia_search_index';
		$sql_search = "CREATE TABLE $table_search_index (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			object_id bigint(20) NOT NULL,
			object_type varchar(50) NOT NULL,
			title text NOT NULL,
			content longtext NOT NULL,
			metadata text,
			indexed_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY  (id),
			KEY object_id (object_id),
			KEY object_type (object_type),
			FULLTEXT KEY search_index (title, content)
		) $charset_collate;";
		dbDelta( $sql_search );

		$table_metrics = $wpdb->prefix . 'healthedia_metrics';
		$sql_metrics = "CREATE TABLE $table_metrics (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			object_id bigint(20) NOT NULL,
			object_type varchar(50) NOT NULL,
			views bigint(20) DEFAULT 0 NOT NULL,
			downloads bigint(20) DEFAULT 0 NOT NULL,
			citations bigint(20) DEFAULT 0 NOT NULL,
			last_updated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY object_idx (object_id, object_type)
		) $charset_collate;";
		dbDelta( $sql_metrics );
	}
}
