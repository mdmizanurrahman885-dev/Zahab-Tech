<?php
/**
 * "অনলাইন পেমেন্ট" gateway: customer sends money by MFS or bank, then enters sender + transaction ID.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ZCS_Gateway_Online extends WC_Payment_Gateway {

	const MFS = array(
		'bkash'   => array( 'bKash', '#E2136E' ),
		'nagad'   => array( 'Nagad', '#E8511E' ),
		'rocket'  => array( 'Rocket', '#8C3494' ),
		'upay'    => array( 'Upay', '#0054A6' ),
		'cellfin' => array( 'CellFin', '#00843D' ),
	);

	public function __construct() {
		$this->id                 = 'zcs_online';
		$this->method_title       = 'অনলাইন পেমেন্ট (bKash / Nagad / Rocket / ব্যাংক)';
		$this->method_description = 'গ্রাহক মোবাইল ব্যাংকিং বা ব্যাংকে টাকা পাঠিয়ে Transaction ID দেবেন। অর্ডার "On hold" অবস্থায় আসবে; টাকা মিলিয়ে "Processing" করে দিন।';
		$this->has_fields         = true;

		$this->init_form_fields();
		$this->init_settings();

		$this->title       = $this->get_option( 'title' );
		$this->description = $this->get_option( 'description' );

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	public function init_form_fields() {
		$num_help = 'প্রতি লাইনে একটি নম্বর: নম্বর | বিবরণ। ফাঁকা রাখলে এই অপশন চেকআউটে দেখাবে না।';

		$this->form_fields = array(
			'enabled'     => array(
				'title'   => 'চালু / বন্ধ',
				'type'    => 'checkbox',
				'label'   => 'অনলাইন পেমেন্ট চালু করুন',
				'default' => 'yes',
			),
			'title'       => array(
				'title'   => 'চেকআউটে নাম',
				'type'    => 'text',
				'default' => 'অনলাইন পেমেন্ট',
			),
			'description' => array(
				'title'   => 'ছোট বিবরণ',
				'type'    => 'textarea',
				'default' => '',
			),
			'bkash'       => array(
				'title'       => 'bKash নম্বর',
				'type'        => 'textarea',
				'description' => $num_help,
				'default'     => "01750646599 | পার্সোনাল · Send Money\n01650020096 | পার্সোনাল · Send Money\n01865441898 | পার্সোনাল · Send Money\n01843135010 | এজেন্ট · Cash Out",
			),
			'nagad'       => array(
				'title'       => 'Nagad নম্বর',
				'type'        => 'textarea',
				'description' => $num_help,
				'default'     => "01750646599 | পার্সোনাল · Send Money\n01865441898 | পার্সোনাল · Send Money",
			),
			'rocket'      => array(
				'title'       => 'Rocket নম্বর',
				'type'        => 'textarea',
				'description' => $num_help . ' Rocket নম্বর ১২ ডিজিটের (মোবাইল নম্বর + শেষে একটি ডিজিট)।',
				'default'     => '017506465999 | পার্সোনাল · Send Money',
			),
			'upay'        => array(
				'title'       => 'Upay নম্বর',
				'type'        => 'textarea',
				'description' => $num_help,
				'default'     => '',
			),
			'cellfin'     => array(
				'title'       => 'CellFin নম্বর',
				'type'        => 'textarea',
				'description' => $num_help,
				'default'     => '01750646599 | Send Money',
			),
			'banks'       => array(
				'title'       => 'ব্যাংক অ্যাকাউন্ট',
				'type'        => 'textarea',
				'css'         => 'min-height:110px;',
				'description' => 'প্রতি লাইনে একটি ব্যাংক: ছোট নাম | ব্যাংকের পুরো নাম | অ্যাকাউন্টের নাম | অ্যাকাউন্ট নম্বর | শাখা | রাউটিং নম্বর',
				'default'     => "IBBL | Islami Bank Bangladesh PLC | MD. MIZANUR RAHMAN | 20501300205501710 | Savar | 125263914\nDBBL | Dutch-Bangla Bank PLC | MD. MIZANUR RAHMAN | 1371570429928 | Savar | 090263881\nEBL | Eastern Bank PLC | MD. MIZANUR RAHMAN | 1701440031938 | Savar | 085263889",
			),
			'charge_note' => array(
				'title'   => 'চার্জ সংক্রান্ত নোট',
				'type'    => 'text',
				'default' => 'রেগুলার চার্জ প্রযোজ্য। চার্জসহ টাকা পাঠান।',
			),
		);
	}

	private function lines( $text ) {
		$out = array();
		foreach ( preg_split( '/\r\n|\r|\n/', (string) $text ) as $line ) {
			$line = trim( $line );
			if ( '' !== $line ) {
				$out[] = array_map( 'trim', explode( '|', $line ) );
			}
		}
		return $out;
	}

	public function get_providers() {
		$providers = array();

		foreach ( self::MFS as $key => $meta ) {
			$rows = array();
			foreach ( $this->lines( $this->get_option( $key ) ) as $parts ) {
				$rows[] = array(
					'number' => $parts[0],
					'label'  => isset( $parts[1] ) ? $parts[1] : '',
				);
			}
			if ( $rows ) {
				$providers[ $key ] = array(
					'name'  => $meta[0],
					'color' => $meta[1],
					'kind'  => 'mfs',
					'rows'  => $rows,
				);
			}
		}

		foreach ( $this->lines( $this->get_option( 'banks' ) ) as $parts ) {
			$parts = array_pad( $parts, 6, '' );
			$key   = 'bank_' . sanitize_key( $parts[0] );
			if ( 'bank_' === $key ) {
				continue;
			}
			$providers[ $key ] = array(
				'name'    => $parts[0],
				'color'   => '',
				'kind'    => 'bank',
				'bank'    => $parts[1],
				'holder'  => $parts[2],
				'account' => $parts[3],
				'branch'  => $parts[4],
				'routing' => $parts[5],
			);
		}

		return $providers;
	}

	public function is_available() {
		return parent::is_available() && ! empty( $this->get_providers() );
	}

	private function copy_btn( $value ) {
		return '<button type="button" class="zcs-op-copy" data-copy="' . esc_attr( preg_replace( '/\s+/', '', $value ) ) . '">কপি</button>';
	}

	public function payment_fields() {
		$providers = $this->get_providers();

		if ( $this->description ) {
			echo wpautop( wp_kses_post( $this->description ) ); // phpcs:ignore WordPress.Security.EscapeOutput
		}

		$total = WC()->cart ? WC()->cart->get_total() : '';
		$first = array_key_first( $providers );
		$pick  = 'কোন মাধ্যমে পাঠাবেন';

		echo '<div class="zcs-op">';
		echo '<p class="zcs-op-lead">মোট <strong>' . wp_kses_post( $total ) . '</strong> পাঠিয়ে নিচে তথ্য দিন।</p>';

		$groups = array(
			'mfs'  => 'মোবাইল ব্যাংকিং',
			'bank' => 'ব্যাংক ট্রান্সফার',
		);
		foreach ( $groups as $kind => $caption ) {
			$items = wp_list_filter( $providers, array( 'kind' => $kind ) );
			if ( ! $items ) {
				continue;
			}
			echo '<fieldset class="zcs-op-group"><legend class="zcs-op-cap">' . esc_html( $caption ) . '</legend><div class="zcs-op-tiles">';
			foreach ( $items as $key => $p ) {
				$id = 'zcs_p_' . $key;
				printf(
					'<input type="radio" class="zcs-op-radio" name="zcs_provider" id="%1$s" value="%2$s" data-kind="%3$s"%4$s><label for="%1$s" class="zcs-op-tile"%5$s>%6$s</label>',
					esc_attr( $id ),
					esc_attr( $key ),
					esc_attr( $kind ),
					checked( $key, $first, false ),
					$p['color'] ? ' style="--brand:' . esc_attr( $p['color'] ) . '"' : '',
					esc_html( $p['name'] )
				);
			}
			echo '</div></fieldset>';
		}

		foreach ( $providers as $key => $p ) {
			printf( '<div class="zcs-op-panel" data-p="%s"%s>', esc_attr( $key ), $key === $first ? '' : ' hidden' );

			if ( 'mfs' === $p['kind'] ) {
				echo '<p class="zcs-op-how">নিচের যেকোনো <strong>' . esc_html( $p['name'] ) . '</strong> নম্বরে টাকা পাঠান:</p><ul class="zcs-op-nums">';
				foreach ( $p['rows'] as $row ) {
					echo '<li><div><span class="zcs-op-num">' . esc_html( $row['number'] ) . '</span>';
					if ( $row['label'] ) {
						echo '<span class="zcs-op-tag">' . esc_html( $row['label'] ) . '</span>';
					}
					echo '</div>' . $this->copy_btn( $row['number'] ) . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput
				}
				echo '</ul>';
			} else {
				echo '<dl class="zcs-op-bank">';
				$fields = array(
					'ব্যাংক'              => array( $p['bank'], false ),
					'অ্যাকাউন্টের নাম'     => array( $p['holder'], false ),
					'অ্যাকাউন্ট নম্বর'     => array( $p['account'], true ),
					'শাখা'                => array( $p['branch'], false ),
					'রাউটিং নম্বর'         => array( $p['routing'], true ),
				);
				foreach ( $fields as $label => $f ) {
					if ( '' === $f[0] ) {
						continue;
					}
					echo '<div><dt>' . esc_html( $label ) . '</dt><dd><span' . ( $f[1] ? ' class="zcs-op-num"' : '' ) . '>' . esc_html( $f[0] ) . '</span>';
					echo $f[1] ? $this->copy_btn( $f[0] ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput
					echo '</dd></div>';
				}
				echo '</dl>';
			}
			echo '</div>';
		}

		echo '<div class="zcs-op-fields">';
		echo '<p class="form-row"><label for="zcs_sender"><span class="zcs-op-sender-label">যে নম্বর থেকে পাঠিয়েছেন</span> <abbr class="required" title="required">*</abbr></label>';
		echo '<input type="text" class="input-text" id="zcs_sender" name="zcs_sender" inputmode="tel" autocomplete="off" aria-required="true" placeholder="01XXXXXXXXX"></p>';
		echo '<p class="form-row"><label for="zcs_trx"><span class="zcs-op-trx-label">Transaction ID</span> <abbr class="required" title="required">*</abbr></label>';
		echo '<input type="text" class="input-text zcs-op-trx" id="zcs_trx" name="zcs_trx" maxlength="30" autocomplete="off" autocapitalize="characters" spellcheck="false" aria-required="true" placeholder="যেমন: 9K7A2B4XQ1"></p>';
		echo '</div>';

		$note = $this->get_option( 'charge_note' );
		if ( $note ) {
			echo '<p class="zcs-op-note"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 8v5M12 16.5v.01"/></svg><span>' . esc_html( $note ) . '</span></p>';
		}
		echo '</div>';
	}

	private function posted() {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- WooCommerce verifies the checkout nonce before gateway hooks run.
		$provider = isset( $_POST['zcs_provider'] ) ? sanitize_key( wp_unslash( $_POST['zcs_provider'] ) ) : '';
		$sender   = isset( $_POST['zcs_sender'] ) ? sanitize_text_field( wp_unslash( $_POST['zcs_sender'] ) ) : '';
		$trx      = isset( $_POST['zcs_trx'] ) ? sanitize_text_field( wp_unslash( $_POST['zcs_trx'] ) ) : '';
		// phpcs:enable

		return array(
			'provider' => $provider,
			'sender'   => $sender,
			'trx'      => strtoupper( preg_replace( '/\s+/', '', $trx ) ),
		);
	}

	private function trx_used( $trx ) {
		$ids = wc_get_orders(
			array(
				'limit'      => 1,
				'return'     => 'ids',
				'meta_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery
					array(
						'key'   => '_zcs_trx',
						'value' => $trx,
					),
				),
			)
		);
		return ! empty( $ids );
	}

	public function validate_fields() {
		$data      = $this->posted();
		$providers = $this->get_providers();

		if ( ! isset( $providers[ $data['provider'] ] ) ) {
			wc_add_notice( 'কোন মাধ্যমে টাকা পাঠিয়েছেন সেটি বেছে নিন।', 'error' );
			return false;
		}

		$ok = true;

		if ( 'mfs' === $providers[ $data['provider'] ]['kind'] ) {
			$digits = preg_replace( '/\D/', '', $data['sender'] );
			if ( 0 === strpos( $digits, '88' ) ) {
				$digits = substr( $digits, 2 );
			}
			if ( ! preg_match( '/^01[3-9]\d{8,9}$/', $digits ) ) {
				wc_add_notice( 'যে নম্বর থেকে টাকা পাঠিয়েছেন সেটি সঠিকভাবে লিখুন (যেমন: 01712345678)।', 'error' );
				$ok = false;
			}
		} elseif ( mb_strlen( $data['sender'] ) < 3 ) {
			wc_add_notice( 'যে অ্যাকাউন্ট থেকে টাকা পাঠিয়েছেন তার নাম বা নম্বর লিখুন।', 'error' );
			$ok = false;
		}

		if ( ! preg_match( '/^[A-Z0-9\-]{6,30}$/', $data['trx'] ) ) {
			wc_add_notice( 'সঠিক Transaction ID লিখুন। এতে শুধু ইংরেজি অক্ষর ও সংখ্যা থাকে (৬ থেকে ৩০ অক্ষর)।', 'error' );
			$ok = false;
		} elseif ( $this->trx_used( $data['trx'] ) ) {
			wc_add_notice( 'এই Transaction ID দিয়ে আগেই একটি অর্ডার হয়েছে। সঠিক ID দিন অথবা আমাদের WhatsApp-এ যোগাযোগ করুন।', 'error' );
			$ok = false;
		}

		return $ok;
	}

	public function process_payment( $order_id ) {
		$order     = wc_get_order( $order_id );
		$data      = $this->posted();
		$providers = $this->get_providers();
		$name      = isset( $providers[ $data['provider'] ] ) ? $providers[ $data['provider'] ]['name'] : $data['provider'];

		$order->update_meta_data( '_zcs_provider', $name );
		$order->update_meta_data( '_zcs_sender', $data['sender'] );
		$order->update_meta_data( '_zcs_trx', $data['trx'] );
		$order->set_transaction_id( $data['trx'] );
		$order->update_status(
			'on-hold',
			sprintf( '%s পেমেন্ট যাচাই বাকি। প্রেরক: %s, Transaction ID: %s', $name, $data['sender'], $data['trx'] )
		);

		wc_reduce_stock_levels( $order_id );
		WC()->cart->empty_cart();

		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);
	}
}
