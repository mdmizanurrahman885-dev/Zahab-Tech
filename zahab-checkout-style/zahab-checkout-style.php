<?php
/**
 * Plugin Name: Zahab Checkout Style
 * Description: Zahab Tech-এর জন্য পরিষ্কার, মোবাইল-ফ্রেন্ডলি Cart ও Checkout পেজ ডিজাইন।
 * Version:     1.1.1
 * Author:      ZAHAB TECH
 * Requires Plugins: woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ZCS_VERSION', '1.1.1' );

add_action( 'before_woocommerce_init', function () {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );

add_action( 'plugins_loaded', function () {
	if ( class_exists( 'WC_Payment_Gateway' ) ) {
		require_once __DIR__ . '/includes/class-zcs-gateway.php';
	}
} );

add_filter( 'woocommerce_payment_gateways', function ( $gateways ) {
	if ( class_exists( 'ZCS_Gateway_Online' ) ) {
		$gateways[] = 'ZCS_Gateway_Online';
	}
	return $gateways;
} );

function zcs_payment_summary( $order ) {
	$trx = $order->get_meta( '_zcs_trx' );
	if ( ! $trx ) {
		return array();
	}
	return array(
		'মাধ্যম'          => $order->get_meta( '_zcs_provider' ),
		'প্রেরক'          => $order->get_meta( '_zcs_sender' ),
		'Transaction ID' => $trx,
	);
}

add_action( 'woocommerce_admin_order_data_after_billing_address', function ( $order ) {
	$rows = zcs_payment_summary( $order );
	if ( ! $rows ) {
		return;
	}
	echo '<div style="margin-top:12px;padding:10px 12px;background:#F6F8FB;border:1px solid #E3E8EF;border-radius:6px;">';
	echo '<h3 style="margin:0 0 6px;">অনলাইন পেমেন্ট তথ্য</h3>';
	foreach ( $rows as $label => $value ) {
		echo '<p style="margin:2px 0;"><strong>' . esc_html( $label ) . ':</strong> <span style="font-family:monospace;font-size:13px;">' . esc_html( $value ) . '</span></p>';
	}
	echo '<p style="margin:6px 0 0;color:#6B7280;">আপনার অ্যাপের লেনদেনের তালিকায় এই ID মিলিয়ে অর্ডারটি Processing করুন।</p>';
	echo '</div>';
} );

add_filter( 'woocommerce_get_order_item_totals', function ( $totals, $order ) {
	$rows = zcs_payment_summary( $order );
	if ( $rows ) {
		$totals['zcs_payment'] = array(
			'label' => 'পেমেন্ট তথ্য:',
			'value' => esc_html( $rows['মাধ্যম'] . ' · ' . $rows['প্রেরক'] . ' · TrxID: ' . $rows['Transaction ID'] ),
		);
	}
	return $totals;
}, 10, 2 );

$zcs_add_column = function ( $columns ) {
	$out = array();
	foreach ( $columns as $key => $label ) {
		$out[ $key ] = $label;
		if ( 'order_status' === $key ) {
			$out['zcs_trx'] = 'পেমেন্ট / TrxID';
		}
	}
	if ( ! isset( $out['zcs_trx'] ) ) {
		$out['zcs_trx'] = 'পেমেন্ট / TrxID';
	}
	return $out;
};
$zcs_render_column = function ( $column, $order ) {
	if ( 'zcs_trx' !== $column ) {
		return;
	}
	$order = $order instanceof WC_Order ? $order : wc_get_order( $order );
	$rows  = $order ? zcs_payment_summary( $order ) : array();
	if ( $rows ) {
		echo esc_html( $rows['মাধ্যম'] ) . '<br><code>' . esc_html( $rows['Transaction ID'] ) . '</code>';
	} else {
		echo '—';
	}
};
add_filter( 'manage_woocommerce_page_wc-orders_columns', $zcs_add_column );
add_action( 'manage_woocommerce_page_wc-orders_custom_column', $zcs_render_column, 10, 2 );
add_filter( 'manage_edit-shop_order_columns', $zcs_add_column );
add_action( 'manage_shop_order_posts_custom_column', $zcs_render_column, 10, 2 );

add_action( 'wp_enqueue_scripts', function () {
	if ( ! function_exists( 'is_checkout' ) || ! ( is_cart() || is_checkout() ) ) {
		return;
	}
	wp_enqueue_style(
		'zcs-fonts',
		'https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Hind+Siliguri:wght@400;500;600&display=swap',
		array(),
		null
	);
	wp_enqueue_style(
		'zcs-style',
		plugin_dir_url( __FILE__ ) . 'assets/zahab-checkout.css',
		array( 'zcs-fonts' ),
		ZCS_VERSION
	);
	if ( is_checkout() ) {
		wp_enqueue_script(
			'zcs-checkout',
			plugin_dir_url( __FILE__ ) . 'assets/zahab-checkout.js',
			array( 'jquery' ),
			ZCS_VERSION,
			true
		);
	}
}, 99 );

add_filter( 'body_class', function ( $classes ) {
	if ( function_exists( 'is_checkout' ) && ( is_cart() || is_checkout() ) ) {
		$classes[] = 'zcs';
	}
	return $classes;
} );

add_filter( 'woocommerce_checkout_fields', function ( $fields ) {
	unset(
		$fields['billing']['billing_company'],
		$fields['billing']['billing_address_2'],
		$fields['billing']['billing_postcode']
	);

	$billing = array(
		'billing_first_name' => array( 'নাম', 'আপনার নাম', true, 10, 'form-row-first' ),
		'billing_last_name'  => array( 'নামের শেষ অংশ', 'ঐচ্ছিক', false, 20, 'form-row-last' ),
		'billing_phone'      => array( 'মোবাইল নম্বর', '01XXXXXXXXX', true, 30, 'form-row-wide' ),
		'billing_email'      => array( 'ইমেইল', 'ঐচ্ছিক — অর্ডার আপডেট পেতে', false, 40, 'form-row-wide' ),
		'billing_country'    => array( 'দেশ', '', true, 45, 'form-row-wide' ),
		'billing_state'      => array( 'জেলা', 'জেলা বেছে নিন', true, 50, 'form-row-first' ),
		'billing_city'       => array( 'উপজেলা / থানা', 'যেমন: সাভার', true, 60, 'form-row-last' ),
		'billing_address_1'  => array( 'সম্পূর্ণ ঠিকানা', 'বাড়ি নং, রাস্তা, এলাকা', true, 70, 'form-row-wide' ),
	);

	foreach ( $billing as $key => $cfg ) {
		if ( ! isset( $fields['billing'][ $key ] ) ) {
			continue;
		}
		$fields['billing'][ $key ]['label']       = $cfg[0];
		$fields['billing'][ $key ]['placeholder'] = $cfg[1];
		$fields['billing'][ $key ]['required']    = $cfg[2];
		$fields['billing'][ $key ]['priority']    = $cfg[3];
		$fields['billing'][ $key ]['class']       = array( $cfg[4] );
	}

	if ( isset( $fields['order']['order_comments'] ) ) {
		$fields['order']['order_comments']['label']       = 'অর্ডার নোট';
		$fields['order']['order_comments']['placeholder'] = 'বিশেষ কোনো নির্দেশনা থাকলে লিখুন (ঐচ্ছিক)';
	}

	return $fields;
}, 20 );

// Locale defaults override field labels/priority for some countries, so re-apply for address fields.
add_filter( 'woocommerce_default_address_fields', function ( $fields ) {
	foreach ( array( 'company', 'address_2', 'postcode' ) as $k ) {
		if ( isset( $fields[ $k ] ) ) {
			$fields[ $k ]['required'] = false;
		}
	}
	return $fields;
}, 20 );

add_filter( 'woocommerce_order_button_text', function () {
	return 'অর্ডার নিশ্চিত করুন';
} );

add_action( 'woocommerce_review_order_after_submit', 'zcs_trust_badges' );
add_action( 'woocommerce_after_cart_totals', 'zcs_trust_badges' );
function zcs_trust_badges() {
	$check = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>';
	echo '<ul class="zcs-trust">';
	foreach ( array( '৭ দিন রিটার্ন', '১০০% অরিজিনাল', 'নিরাপদ পেমেন্ট' ) as $label ) {
		echo '<li>' . $check . esc_html( $label ) . '</li>';
	}
	echo '</ul>';
}

add_action( 'woocommerce_before_checkout_form', function () {
	echo '<div class="zcs-steps" aria-label="Checkout progress">'
		. '<span class="zcs-step is-done"><b>✓</b>কার্ট</span>'
		. '<span class="zcs-step is-active"><b>2</b>চেকআউট</span>'
		. '<span class="zcs-step"><b>3</b>কনফার্মেশন</span>'
		. '</div>';
}, 1 );

add_filter( 'woocommerce_cart_item_remove_link', function ( $link ) {
	return str_replace( '&times;', '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>', $link );
} );
