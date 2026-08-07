<?php
class Healthedia_Article_DOI {
	public function generate_doi_on_publish( $post_id, $post, $update ) {
		if ( $post->post_status !== 'publish' ) {
			return;
		}

		$existing_doi = get_post_meta( $post_id, '_healthedia_doi', true );
		if ( empty( $existing_doi ) ) {
			// Format: 10.xxxx/healthedia.year.id
			$year = date('Y', strtotime($post->post_date));
			$doi = sprintf( '10.5555/healthedia.%s.%d', $year, $post_id );
			update_post_meta( $post_id, '_healthedia_doi', $doi );
		}
	}
}
