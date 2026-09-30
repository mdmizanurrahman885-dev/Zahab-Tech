<?php
/**
 * Billing field layout, Bangla labels and Bangladesh phone validation.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ZCS_Fields {

	public static function init() {
		add_filter( 'woocommerce_default_address_fields', array( __CLASS__, 'address_fields' ), 20 );
		add_filter( 'woocommerce_get_country_locale', array( __CLASS__, 'bd_locale' ), 20 );
		add_filter( 'woocommerce_checkout_fields', array( __CLASS__, 'checkout_fields' ), 20 );
		add_filter( 'woocommerce_countries_allowed_countries', array( __CLASS__, 'lock_bd' ), 20 );
		add_filter( 'woocommerce_countries_shipping_countries', array( __CLASS__, 'lock_bd' ), 20 );
		add_filter( 'default_checkout_billing_country', array( __CLASS__, 'default_country' ), 20 );
		add_filter( 'default_checkout_shipping_country', array( __CLASS__, 'default_country' ), 20 );
		add_action( 'woocommerce_after_checkout_validation', array( __CLASS__, 'validate_phone' ), 20, 2 );
	}

	/**
	 * Address fields feed the default country locale that address-i18n.js re-applies in the browser,
	 * so labels, order and requirements are set here rather than only on checkout fields.
	 */
	public static function address_fields( $fields ) {
		unset( $fields['company'], $fields['address_2'] );

		$spec = array(
			'first_name' => array( 'নাম', 'যেমন: রহিম উদ্দিন', true, 10, 'form-row-first' ),
			'last_name'  => array( 'নামের শেষ অংশ', 'ঐচ্ছিক', false, 20, 'form-row-last' ),
			'country'    => array( 'দেশ', '', true, 45, 'form-row-wide' ),
			'state'      => array( 'জেলা', 'জেলা বেছে নিন', true, 50, 'form-row-first' ),
			'city'       => array( 'উপজেলা / থানা', 'যেমন: সাভার', true, 60, 'form-row-last' ),
			'address_1'  => array( 'সম্পূর্ণ ঠিকানা', 'বাড়ি নং, রোড, এলাকা', true, 70, 'form-row-wide' ),
		);
		foreach ( $spec as $key => $s ) {
			if ( ! isset( $fields[ $key ] ) ) {
				continue;
			}
			$fields[ $key ]['label']       = $s[0];
			$fields[ $key ]['placeholder'] = $s[1];
			$fields[ $key ]['required']    = $s[2];
			$fields[ $key ]['priority']    = $s[3];
			$fields[ $key ]['class']       = array( $s[4] );
		}
		if ( isset( $fields['postcode'] ) ) {
			$fields['postcode']['required'] = false;
			$fields['postcode']['hidden']   = true;
			$fields['postcode']['priority'] = 90;
			$fields['postcode']['class']    = array( 'form-row-wide', 'zcs-hidden-field' );
		}
		return $fields;
	}

	public static function bd_locale( $locale ) {
		$locale['BD'] = array_merge(
			isset( $locale['BD'] ) ? $locale['BD'] : array(),
			array(
				'state'    => array(
					'label'    => 'জেলা',
					'required' => true,
					'priority' => 50,
				),
				'city'     => array(
					'label'    => 'উপজেলা / থানা',
					'required' => true,
					'priority' => 60,
				),
				'address_1' => array(
					'label'    => 'সম্পূর্ণ ঠিকানা',
					'priority' => 70,
				),
				'postcode' => array(
					'required' => false,
					'hidden'   => true,
				),
			)
		);
		return $locale;
	}

	public static function checkout_fields( $fields ) {
		if ( isset( $fields['billing']['billing_phone'] ) ) {
			$fields['billing']['billing_phone'] = array_merge(
				$fields['billing']['billing_phone'],
				array(
					'label'       => 'মোবাইল নম্বর',
					'placeholder' => '01XXXXXXXXX',
					'required'    => true,
					'priority'    => 30,
					'class'       => array( 'form-row-wide' ),
					'custom_attributes' => array( 'inputmode' => 'tel' ),
				)
			);
		}
		if ( isset( $fields['billing']['billing_email'] ) ) {
			$fields['billing']['billing_email'] = array_merge(
				$fields['billing']['billing_email'],
				array(
					'label'       => 'ইমেইল',
					'placeholder' => 'ঐচ্ছিক — অর্ডার আপডেট পেতে',
					'required'    => false,
					'priority'    => 40,
					'class'       => array( 'form-row-wide' ),
				)
			);
		}
		if ( isset( $fields['order']['order_comments'] ) ) {
			$fields['order']['order_comments']['label']       = 'অর্ডার নোট';
			$fields['order']['order_comments']['placeholder'] = 'বিশেষ কোনো নির্দেশনা থাকলে লিখুন (ঐচ্ছিক)';
		}
		foreach ( array( 'billing', 'shipping' ) as $group ) {
			unset( $fields[ $group ][ $group . '_company' ], $fields[ $group ][ $group . '_address_2' ] );
		}
		return $fields;
	}

	/**
	 * On checkout the store only ships inside Bangladesh, so the country select shows a single locked option.
	 */
	public static function lock_bd( $countries ) {
		if ( is_array( $countries ) && isset( $countries['BD'] ) && function_exists( 'is_checkout' ) && is_checkout() ) {
			return array( 'BD' => $countries['BD'] );
		}
		return $countries;
	}

	public static function default_country( $country ) {
		return $country ? $country : 'BD';
	}

	public static function normalize_phone( $phone ) {
		$digits = preg_replace( '/\D/', '', (string) $phone );
		if ( 0 === strpos( $digits, '88' ) ) {
			$digits = substr( $digits, 2 );
		}
		return $digits;
	}

	public static function validate_phone( $data, $errors ) {
		if ( empty( $data['billing_phone'] ) ) {
			return;
		}
		if ( ! preg_match( '/^01[3-9]\d{8,9}$/', self::normalize_phone( $data['billing_phone'] ) ) ) {
			$errors->add(
				'billing_phone_validation',
				'সঠিক মোবাইল নম্বর লিখুন (যেমন: 01712345678)।',
				array( 'id' => 'billing_phone' )
			);
		}
	}
}
