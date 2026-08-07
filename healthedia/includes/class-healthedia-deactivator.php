<?php
class Healthedia_Deactivator {
	public static function deactivate() {
		flush_rewrite_rules();
	}
}
