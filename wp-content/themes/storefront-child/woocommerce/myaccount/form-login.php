<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

do_action( 'woocommerce_before_customer_login_form' ); ?>

    <div class="login-form reg">

		<h2><?php esc_html_e( 'התחבר לחשבון קיים', 'woocommerce' ); ?></h2>

		<form class="woocommerce-form woocommerce-form-login login" method="post" novalidate>

			<?php do_action( 'woocommerce_login_form_start' ); ?>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">				
				<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" placeholder="<?php esc_html_e( 'דוא"ל', 'woocommerce' ); ?>" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) && is_string( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true" /><?php // @codingStandardsIgnoreLine ?>
			</p>
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">				
				<input class="woocommerce-Input woocommerce-Input--text input-text" placeholder="<?php esc_html_e( 'Password', 'woocommerce' ); ?>" type="password" name="password" id="password" autocomplete="current-password" required aria-required="true" />
                <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="woocommerce-LostPassword lost_password"><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></a>                
			</p>

			<?php do_action( 'woocommerce_login_form' ); ?>

            <div class="remember form-row">
                <label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
					<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'Remember me', 'woocommerce' ); ?></span>
				</label>
                <div class="remember-tooltip">
                    <div class="remember-tooltip-inner">
                        <div class="title">השאר מחובר</div>
                        <div class="content">
                            <p>בחירה ב “ זכור אותי” מקטינה את מספר הפעמים שתתבקשו להתחבר ממכשיר זה.</p>
                            <p style="margin: 0;">על מנת לשמור את חשבונכם מאובטח, השתמשו באופציה זו רק במכשירכם האישי</p>
                        </div>
                    </div>
                </div>
            </div>

			<p class="form-row btn">
				<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
				<button type="submit" class="woocommerce-button button woocommerce-form-login__submit<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="login" value="<?php esc_attr_e( 'הכנסו', 'woocommerce' ); ?>"><?php esc_html_e( 'הכנסו', 'woocommerce' ); ?></button>
			</p>

			<?php do_action( 'woocommerce_login_form_end' ); ?>
		</form>

        <div class="to-account">
            <div class="title">אין לך עדיין חשבון</div>
            <a href="<?php echo get_site_url(); ?>/account-create/">יצירת חשבון</a>
        </div>

</div>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
