<?php
class Healthedia_Auth_OTP {
	public static function generate($email) {
		$otp = wp_rand(100000, 999999);
		$hashed = wp_hash_password((string)$otp);
		set_transient('healthedia_otp_' . md5($email), $hashed, 15 * MINUTE_IN_SECONDS);
		return $otp;
	}

	public static function verify($email, $otp) {
		$hashed = get_transient('healthedia_otp_' . md5($email));
		if (!$hashed) return false;

		if (wp_check_password((string)$otp, $hashed)) {
			delete_transient('healthedia_otp_' . md5($email));
			return true;
		}
		return false;
	}
}
