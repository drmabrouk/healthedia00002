<?php
class Healthedia_Auth_Mailer {
	public static function send_otp($email, $otp, $type = 'login') {
		$site_name = get_option('blogname', 'Healthedia');
		$admin_email = get_option('admin_email');

		$headers = array('Content-Type: text/html; charset=UTF-8');
		$headers[] = 'From: ' . $site_name . ' <' . $admin_email . '>';

		$subject = $type === 'register' ? 'Verify Your Academic Account - ' . $site_name : 'Authentication Request - ' . $site_name;

		$message = '
		<div style="font-family: monospace, sans-serif; color: #111111; max-width: 500px; margin: 0 auto; border: 1px solid #E0E0E0; border-radius: 8px; overflow: hidden;">
			<div style="background-color: #FAFAFA; border-bottom: 1px solid #E0E0E0; padding: 20px; text-align: center;">
				<h1 style="font-family: sans-serif; font-size: 24px; font-weight: bold; margin: 0; text-transform: uppercase;">'.esc_html($site_name).'</h1>
				<p style="font-size: 12px; color: #666; margin: 5px 0 0 0; text-transform: uppercase;">Authentication Services</p>
			</div>
			<div style="padding: 30px 20px;">
				<p style="font-size: 14px; line-height: 1.6; margin-bottom: 20px;">
					A request was made to authenticate this email address on the '.esc_html($site_name).' platform. Please use the secure one-time password (OTP) below to proceed.
				</p>
				<div style="background-color: #F3F4F6; border: 1px solid #E5E7EB; border-radius: 6px; padding: 20px; text-align: center; margin-bottom: 20px;">
					<span style="font-size: 32px; font-weight: bold; letter-spacing: 4px;">'.esc_html($otp).'</span>
				</div>
				<p style="font-size: 12px; color: #666; margin: 0;">
					This code is strictly valid for <strong>15 minutes</strong> from the time of issuance. Do not share this code with anyone.
				</p>
			</div>
			<div style="background-color: #FAFAFA; border-top: 1px solid #E0E0E0; padding: 15px 20px; text-align: center; font-size: 10px; color: #999;">
				If you did not request this code, you may safely ignore this email.
			</div>
		</div>';

		wp_mail($email, $subject, $message, $headers);
	}
}
