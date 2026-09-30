<?php
/**
 * Plugin Name:          Zahab Checkout Style
 * Description:          Zahab Tech-এর জন্য পরিষ্কার, মোবাইল-ফ্রেন্ডলি Cart ও Checkout পেজ ডিজাইন — সাথে "অনলাইন পেমেন্ট" gateway।
 * Version:              2.0.0
 * Author:               ZAHAB TECH
 * Requires at least:    6.0
 * Requires PHP:         7.4
 * WC requires at least: 8.0
 * Requires Plugins:     woocommerce
 * Text Domain:          zahab-checkout-style
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ZCS_VERSION', '2.0.0' );
define( 'ZCS_FILE', __FILE__ );
define( 'ZCS_DIR', __DIR__ );

add_action( 'before_woocommerce_init', function () {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );

add_action( 'plugins_loaded', function () {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}
	require_once ZCS_DIR . '/includes/class-zcs-fields.php';
	ZCS_Fields::init();
	if ( class_exists( 'WC_Payment_Gateway' ) ) {
		require_once ZCS_DIR . '/includes/class-zcs-gateway.php';
	}
} );

add_filter( 'woocommerce_payment_gateways', function ( $gateways ) {
	if ( class_exists( 'ZCS_Gateway_Online' ) ) {
		$gateways[] = 'ZCS_Gateway_Online';
	}
	return $gateways;
} );

/**
 * True on the cart/checkout page and inside WooCommerce checkout AJAX requests.
 */
function zcs_is_context() {
	if ( ! function_exists( 'is_cart' ) || ! did_action( 'wp' ) && ! wp_doing_ajax() ) {
		return false;
	}
	return is_cart() || is_checkout();
}

/* ─────────────── Assets ─────────────── */

add_action( 'wp_enqueue_scripts', function () {
	if ( ! function_exists( 'is_cart' ) || ! ( is_cart() || is_checkout() ) ) {
		return;
	}

	$css = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) || ! file_exists( ZCS_DIR . '/assets/zahab-checkout.min.css' )
		? 'zahab-checkout.css'
		: 'zahab-checkout.min.css';

	wp_enqueue_style(
		'zcs-fonts',
		'https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Hind+Siliguri:wght@400;500;600;700&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'zcs-style', plugins_url( 'assets/' . $css, ZCS_FILE ), array( 'zcs-fonts' ), ZCS_VERSION );
	wp_enqueue_script( 'zcs-checkout', plugins_url( 'assets/zahab-checkout.js', ZCS_FILE ), array( 'jquery' ), ZCS_VERSION, true );
}, 99 );

add_filter( 'wp_resource_hints', function ( $urls, $relation ) {
	if ( 'preconnect' === $relation && function_exists( 'is_cart' ) && ( is_cart() || is_checkout() ) ) {
		$urls[] = array( 'href' => 'https://fonts.googleapis.com' );
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}
	return $urls;
}, 10, 2 );

add_filter( 'body_class', function ( $classes ) {
	if ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() ) ) {
		$classes[] = 'zcs';
		$classes[] = is_cart() ? 'zcs-cart' : 'zcs-co';
	}
	return $classes;
} );

/* ─────────────── Bangla strings (cart/checkout only) ─────────────── */

function zcs_strings() {
	return array(
		'Cart totals'                   => 'কার্ট সারসংক্ষেপ',
		'Coupon code'                   => 'কুপন কোড',
		'Apply coupon'                  => 'কুপন প্রয়োগ',
		'Update cart'                   => 'কার্ট আপডেট',
		'Proceed to checkout'           => 'চেকআউট এ যান →',
		'Product'                       => 'পণ্য',
		'Price'                         => 'দাম',
		'Quantity'                      => 'পরিমাণ',
		'Subtotal'                      => 'সাবটোটাল',
		'Total'                         => 'মোট',
		'Shipping'                      => 'ডেলিভারি',
		'Billing details'               => 'বিলিং তথ্য',
		'Billing &amp; Shipping'        => 'বিলিং তথ্য',
		'Additional information'        => 'অতিরিক্ত তথ্য',
		'Ship to a different address?'  => 'অন্য ঠিকানায় পাঠাতে চান?',
		'Returning customer?'           => 'পুরনো কাস্টমার?',
		'Click here to login'           => 'এখানে লগইন করুন',
		'Have a coupon?'                => 'কুপন আছে?',
		'Click here to enter your code' => 'এখানে কুপন কোড দিন',
		'If you have a coupon code, please apply it below.' => 'কুপন কোড থাকলে নিচে লিখে প্রয়োগ করুন।',
		'optional'                      => 'ঐচ্ছিক',
		'Remove item'                   => 'পণ্য সরান',
		'Cart updated.'                 => 'কার্ট আপডেট হয়েছে।',
		'Coupon code applied successfully.' => 'কুপন প্রয়োগ হয়েছে।',
		'Coupon has been removed.'      => 'কুপন সরানো হয়েছে।',
		'[Remove]'                      => '[সরান]',
		'Change address'                => 'ঠিকানা বদলান',
		'Shipping to %s.'               => '%s এ ডেলিভারি।',
		'Shipping options will be updated during checkout.' => 'চেকআউটে ডেলিভারি অপশন দেখাবে।',
		'%s is a required field.'       => '%s দেওয়া আবশ্যক।',
		'%s is not a valid phone number.' => '%s সঠিক নয়। যেমন: 01712345678',
		'%s is not a valid email address.' => '%s সঠিক নয়।',
		'Please read and accept the terms and conditions to proceed with your order.' => 'অর্ডার করতে শর্তাবলীতে টিক দিন।',
		'Invalid payment method.'       => 'একটি পেমেন্ট পদ্ধতি বেছে নিন।',
		'Please enter an address to continue.' => 'ঠিকানা লিখুন।',
		'No shipping method has been selected. Please double check your address, or contact us if you need any help.' => 'ডেলিভারি অপশন বেছে নিন। সমস্যা হলে আমাদের WhatsApp-এ যোগাযোগ করুন।',
		'There are no shipping options available. Please ensure that your address has been entered correctly, or contact us if you need any help.' => 'এই ঠিকানায় ডেলিভারি অপশন পাওয়া যায়নি। ঠিকানা মিলিয়ে দেখুন অথবা আমাদের WhatsApp-এ যোগাযোগ করুন।',
		'We were unable to process your order, please try again.' => 'অর্ডারটি সম্পন্ন করা যায়নি। আবার চেষ্টা করুন।',
		'Sorry, your session has expired.' => 'সেশন শেষ হয়ে গেছে। পেজটি আবার লোড করুন।',
	);
}

add_filter( 'gettext_woocommerce', function ( $translation, $text ) {
	static $map = null;
	if ( null === $map ) {
		$map = zcs_strings();
	}
	if ( isset( $map[ $text ] ) && zcs_is_context() ) {
		return $map[ $text ];
	}
	return $translation;
}, 20, 2 );

add_filter( 'gettext_with_context_woocommerce', function ( $translation, $text, $context ) {
	if ( 'checkout-validation' !== $context || ! zcs_is_context() ) {
		return $translation;
	}
	if ( 'Billing %s' === $text ) {
		return '%s';
	}
	if ( 'Shipping %s' === $text ) {
		return 'ডেলিভারি ঠিকানার %s';
	}
	return $translation;
}, 20, 3 );

add_filter( 'woocommerce_order_button_text', function () {
	return 'অর্ডার নিশ্চিত করুন';
} );

/* ─────────────── Shared markup ─────────────── */

function zcs_check_svg( $size = 13 ) {
	return '<svg width="' . (int) $size . '" height="' . (int) $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>';
}

function zcs_trust_badges() {
	echo '<ul class="zcs-trust">';
	foreach ( array( '৭ দিন রিটার্ন', '১০০% অরিজিনাল', 'নিরাপদ পেমেন্ট' ) as $label ) {
		echo '<li>' . zcs_check_svg() . esc_html( $label ) . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
	echo '</ul>';
}
add_action( 'woocommerce_review_order_after_submit', 'zcs_trust_badges' );
add_action( 'woocommerce_after_cart_totals', 'zcs_trust_badges' );

/* ─────────────── Cart ─────────────── */

// Only shown when a free shipping rate is actually offered, so the strip never promises something the store does not give.
add_action( 'woocommerce_before_cart_table', function () {
	$free = false;
	foreach ( WC()->shipping()->get_packages() as $package ) {
		foreach ( (array) ( $package['rates'] ?? array() ) as $rate ) {
			if ( 'free_shipping' === $rate->get_method_id() ) {
				$free = true;
				break 2;
			}
		}
	}
	if ( $free ) {
		echo '<div class="zcs-free">' . zcs_check_svg( 16 ) . '<span><strong>ফ্রি ডেলিভারি আনলক হয়েছে!</strong> ডেলিভারি অপশন থেকে ফ্রি ডেলিভারি বেছে নিন।</span></div>';
	}
} );

function zcs_in_cart_rows() {
	return did_action( 'woocommerce_before_cart_contents' ) > did_action( 'woocommerce_after_cart_contents' );
}

function zcs_in_review_rows() {
	return did_action( 'woocommerce_review_order_before_cart_contents' ) > did_action( 'woocommerce_review_order_after_cart_contents' );
}

function zcs_product_brand( $product ) {
	$id = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
	foreach ( array( 'product_brand', 'pwb-brand', 'yith_product_brand' ) as $tax ) {
		if ( taxonomy_exists( $tax ) ) {
			$terms = get_the_terms( $id, $tax );
			if ( $terms && ! is_wp_error( $terms ) ) {
				return $terms[0]->name;
			}
		}
	}
	$parent = wc_get_product( $id );
	return $parent ? (string) $parent->get_attribute( 'pa_brand' ) : '';
}

add_filter( 'woocommerce_cart_item_name', function ( $name, $cart_item ) {
	if ( zcs_in_cart_rows() ) {
		// Only the linked title call; the plain call feeds aria-labels and must stay text.
		if ( false !== strpos( $name, '<a ' ) && ! empty( $cart_item['data'] ) ) {
			$brand = zcs_product_brand( $cart_item['data'] );
			if ( $brand ) {
				$name = '<span class="zcs-brand">' . esc_html( $brand ) . '</span>' . $name;
			}
		}
		return $name;
	}
	if ( zcs_in_review_rows() && ! empty( $cart_item['data'] ) ) {
		$img = $cart_item['data']->get_image( array( 96, 96 ), array( 'class' => 'zcs-thumb-img' ) );
		return '<span class="zcs-thumb">' . $img . '</span><span class="zcs-name">' . $name . '</span>';
	}
	return $name;
}, 20, 2 );

add_filter( 'woocommerce_checkout_cart_item_quantity', function ( $html ) {
	return zcs_in_review_rows() ? '' : $html;
}, 20 );

add_filter( 'woocommerce_cart_item_subtotal', function ( $subtotal, $cart_item ) {
	if ( zcs_in_review_rows() && ! empty( $cart_item['quantity'] ) && $cart_item['quantity'] > 1 ) {
		$unit      = WC()->cart->get_product_price( $cart_item['data'] );
		$subtotal .= '<small class="zcs-each">' . $unit . ' × ' . (int) $cart_item['quantity'] . '</small>';
	}
	return $subtotal;
}, 20, 2 );

add_action( 'woocommerce_before_quantity_input_field', function () {
	if ( zcs_in_cart_rows() ) {
		echo '<button type="button" class="zcs-qty-btn" data-dir="-1" aria-label="পরিমাণ কমান">−</button>';
	}
} );
add_action( 'woocommerce_after_quantity_input_field', function () {
	if ( zcs_in_cart_rows() ) {
		echo '<button type="button" class="zcs-qty-btn" data-dir="1" aria-label="পরিমাণ বাড়ান">+</button>';
	}
} );

add_filter( 'woocommerce_cart_item_remove_link', function ( $link ) {
	$icon = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>';
	return str_replace( '&times;', $icon, $link );
} );

add_action( 'init', function () {
	remove_action( 'woocommerce_cart_is_empty', 'wc_empty_cart_message', 10 );
} );
add_action( 'woocommerce_cart_is_empty', function () {
	$shop = wc_get_page_id( 'shop' ) > 0 ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
	echo '<div class="zcs-empty">';
	echo '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"/></svg>';
	echo '<h2>আপনার কার্ট এখনো খালি</h2>';
	echo '<p>পছন্দের পণ্য খুঁজে যোগ করুন</p>';
	echo '<a class="zcs-btn" href="' . esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', $shop ) ) . '">শপিং শুরু করুন</a>';
	echo '</div>';
} );

/* ─────────────── Checkout ─────────────── */

add_action( 'woocommerce_before_checkout_form', function () {
	$check = zcs_check_svg( 14 );
	echo '<a class="zcs-skip" href="#customer_details">অর্ডার ফর্মে যান</a>';
	echo '<ol class="zcs-steps" aria-label="চেকআউটের ধাপ">'
		. '<li class="zcs-step is-done"><span class="zcs-step-n">' . $check . '</span>কার্ট</li>' // phpcs:ignore WordPress.Security.EscapeOutput
		. '<li class="zcs-step is-active" aria-current="step"><span class="zcs-step-n">2</span>চেকআউট</li>'
		. '<li class="zcs-step"><span class="zcs-step-n">3</span>কনফার্মেশন</li>'
		. '</ol>';
}, 1 );

add_filter( 'woocommerce_gateway_icon', function ( $icon, $id ) {
	if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) {
		return $icon;
	}
	$icons = array(
		'zcs_online' => array( '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="2" width="14" height="20" rx="2.5"/><path d="M11 18h2"/></svg>', 'bKash, Nagad, Rocket, CellFin বা ব্যাংক' ),
		'cod'        => array( '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2.5"/><path d="M6 10v.01M18 14v.01"/></svg>', 'পণ্য হাতে পেয়ে টাকা দিন' ),
		'bacs'       => array( '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 10 12 4l9 6"/><path d="M5 10v8M9.5 10v8M14.5 10v8M19 10v8M3 20h18"/></svg>', 'ব্যাংক ট্রান্সফার' ),
	);
	if ( ! isset( $icons[ $id ] ) ) {
		return $icon;
	}
	return '<span class="zcs-gw-ico">' . $icons[ $id ][0] . '</span><span class="zcs-gw-sub">' . esc_html( $icons[ $id ][1] ) . '</span>';
}, 20, 2 );

/* ─────────────── Orders: admin + customer ─────────────── */

function zcs_payment_summary( $order ) {
	$trx = $order->get_meta( '_zcs_trx' );
	if ( ! $trx ) {
		return array();
	}
	return array(
		'provider' => (string) $order->get_meta( '_zcs_provider' ),
		'sender'   => (string) $order->get_meta( '_zcs_sender' ),
		'trx'      => (string) $trx,
	);
}

add_action( 'woocommerce_admin_order_data_after_billing_address', function ( $order ) {
	$p = zcs_payment_summary( $order );
	if ( ! $p ) {
		return;
	}
	echo '<div style="margin-top:12px;padding:10px 12px;background:#F6F8FB;border:1px solid #E3E8EF;border-radius:6px;">';
	echo '<h3 style="margin:0 0 6px;">অনলাইন পেমেন্ট তথ্য</h3>';
	echo '<p style="margin:2px 0;"><strong>মাধ্যম:</strong> ' . esc_html( $p['provider'] ) . '</p>';
	echo '<p style="margin:2px 0;"><strong>প্রেরক:</strong> ' . esc_html( $p['sender'] ) . '</p>';
	echo '<p style="margin:2px 0;"><strong>Transaction ID:</strong> <span style="font-family:ui-monospace,Consolas,monospace;font-size:13px;">' . esc_html( $p['trx'] ) . '</span></p>';
	echo '<p style="margin:6px 0 0;color:#6B7280;">আপনার অ্যাপের লেনদেনের তালিকায় এই ID মিলিয়ে অর্ডারটি Processing করুন।</p>';
	echo '</div>';
} );

add_filter( 'woocommerce_get_order_item_totals', function ( $totals, $order ) {
	$p = zcs_payment_summary( $order );
	if ( $p ) {
		$totals['zcs_payment'] = array(
			'label' => 'পেমেন্ট তথ্য:',
			'value' => esc_html( $p['provider'] . ' · ' . $p['sender'] . ' · TrxID: ' . $p['trx'] ),
		);
	}
	return $totals;
}, 10, 2 );

function zcs_add_order_column( $columns ) {
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
}

function zcs_render_order_column( $column, $order ) {
	if ( 'zcs_trx' !== $column ) {
		return;
	}
	$order = $order instanceof WC_Order ? $order : wc_get_order( $order );
	$p     = $order ? zcs_payment_summary( $order ) : array();
	if ( $p ) {
		echo esc_html( $p['provider'] ) . '<br><code>' . esc_html( $p['trx'] ) . '</code>';
	} else {
		echo '—';
	}
}

add_filter( 'manage_woocommerce_page_wc-orders_columns', 'zcs_add_order_column' );
add_action( 'manage_woocommerce_page_wc-orders_custom_column', 'zcs_render_order_column', 10, 2 );
add_filter( 'manage_edit-shop_order_columns', 'zcs_add_order_column' );
add_action( 'manage_shop_order_posts_custom_column', 'zcs_render_order_column', 10, 2 );
