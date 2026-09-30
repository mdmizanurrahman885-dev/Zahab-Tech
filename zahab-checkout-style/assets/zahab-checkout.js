(function ($) {
	'use strict';

	var $body = $(document.body);

	/* ─────────── Online payment: provider switch ─────────── */
	function syncProvider() {
		$('.zcs-op').each(function () {
			var $root = $(this);
			var $checked = $root.find('.zcs-op-radio:checked');
			var key = $checked.val();
			var isBank = $checked.data('kind') === 'bank';

			$root.find('.zcs-op-panel').each(function () {
				this.hidden = this.getAttribute('data-p') !== key;
			});
			$root.find('.zcs-op-sender-label').text(isBank ? 'যে অ্যাকাউন্ট থেকে পাঠিয়েছেন (নাম বা নম্বর)' : 'যে নম্বর থেকে পাঠিয়েছেন');
			$root.find('.zcs-op-trx-label').text(isBank ? 'Transaction / Reference ID' : 'Transaction ID');
			$root.find('#zcs_sender').attr({
				placeholder: isBank ? 'যেমন: Rahim, A/C শেষ ৪ ডিজিট 1234' : '01XXXXXXXXX',
				inputmode: isBank ? 'text' : 'tel'
			});
		});
	}

	$(document).on('change', '.zcs-op-radio', syncProvider);
	$body.on('updated_checkout payment_method_selected', syncProvider);

	/* ─────────── Copy buttons ─────────── */
	$(document).on('click', '.zcs-op-copy', function (e) {
		e.preventDefault();
		var btn = this;
		var text = btn.getAttribute('data-copy');

		function done() {
			btn.textContent = 'কপি হয়েছে';
			btn.classList.add('is-done');
			clearTimeout(btn._zcsTimer);
			btn._zcsTimer = setTimeout(function () {
				btn.textContent = 'কপি';
				btn.classList.remove('is-done');
			}, 2000);
		}

		function fallback() {
			var ta = document.createElement('textarea');
			ta.value = text;
			ta.setAttribute('readonly', '');
			ta.style.position = 'fixed';
			ta.style.opacity = '0';
			document.body.appendChild(ta);
			ta.select();
			try {
				if (document.execCommand('copy')) {
					done();
				}
			} catch (err) {}
			document.body.removeChild(ta);
		}

		if (navigator.clipboard && window.isSecureContext) {
			navigator.clipboard.writeText(text).then(done, fallback);
		} else {
			fallback();
		}
	});

	/* ─────────── Cart: quantity stepper with auto update ─────────── */
	var updateTimer = null;

	function scheduleCartUpdate() {
		clearTimeout(updateTimer);
		updateTimer = setTimeout(function () {
			var $btn = $('.woocommerce-cart-form :input[name="update_cart"]');
			if ($btn.length) {
				$btn.prop('disabled', false).attr('aria-disabled', 'false').trigger('click');
			}
		}, 700);
	}

	function syncStepper($wrap) {
		var $input = $wrap.find('input.qty');
		var val = parseFloat($input.val()) || 0;
		var min = parseFloat($input.attr('min'));
		var max = parseFloat($input.attr('max'));
		$wrap.find('.zcs-qty-btn[data-dir="-1"]').prop('disabled', !isNaN(min) && val <= Math.max(min, 1));
		$wrap.find('.zcs-qty-btn[data-dir="1"]').prop('disabled', !isNaN(max) && max > 0 && val >= max);
	}

	function initSteppers() {
		$('.woocommerce-cart-form .quantity').each(function () {
			syncStepper($(this));
		});
	}

	$(document).on('click', '.woocommerce-cart-form .zcs-qty-btn', function (e) {
		e.preventDefault();
		var $wrap = $(this).closest('.quantity');
		var $input = $wrap.find('input.qty');
		var step = parseFloat($input.attr('step')) || 1;
		var min = parseFloat($input.attr('min'));
		var max = parseFloat($input.attr('max'));
		var next = (parseFloat($input.val()) || 0) + step * parseInt(this.getAttribute('data-dir'), 10);

		if (!isNaN(min)) {
			next = Math.max(next, Math.max(min, 1));
		}
		if (!isNaN(max) && max > 0) {
			next = Math.min(next, max);
		}
		if (next === parseFloat($input.val())) {
			return;
		}
		$input.val(next).trigger('change');
		syncStepper($wrap);
		scheduleCartUpdate();
	});

	$(document).on('change', '.woocommerce-cart-form input.qty', function (e) {
		if (e.originalEvent) {
			syncStepper($(this).closest('.quantity'));
			scheduleCartUpdate();
		}
	});

	function markTotalsLive() {
		$('.cart_totals').attr({ 'aria-live': 'polite', 'aria-atomic': 'false' });
	}

	$body.on('updated_wc_div updated_cart_totals', function () {
		initSteppers();
		markTotalsLive();
	});

	/* ─────────── Checkout: order notes behind a link ─────────── */
	function initNotes() {
		var $field = $('#order_comments_field');
		if (!$field.length || $field.data('zcsNotes')) {
			return;
		}
		$field.data('zcsNotes', true);
		if ($.trim($('#order_comments').val())) {
			return;
		}
		var $toggle = $('<button type="button" class="zcs-note-toggle" aria-controls="order_comments_field" aria-expanded="false">+ নোট যোগ করুন</button>');
		$field.hide().before($toggle);
		$toggle.on('click', function () {
			$toggle.remove();
			$field.show();
			$('#order_comments').trigger('focus');
		});
	}

	/* ─────────── Checkout: chosen shipping as a chip ─────────── */
	var shipExpanded = false;

	function collapseShipping() {
		var $list = $('.woocommerce-checkout-review-order-table #shipping_method');
		$list.next('.zcs-ship-change').remove();
		if ($list.find('input[type="radio"]').length < 2 || !$list.find('input:checked').length) {
			return;
		}
		if (shipExpanded) {
			$list.removeClass('zcs-collapsed');
			return;
		}
		$list.addClass('zcs-collapsed');
		$('<button type="button" class="zcs-ship-change">ডেলিভারি অপশন পরিবর্তন করুন</button>')
			.insertAfter($list)
			.on('click', function () {
				shipExpanded = true;
				$list.removeClass('zcs-collapsed');
				$(this).remove();
			});
	}

	/* ─────────── Checkout: place order loading state ─────────── */
	$('form.checkout').on('checkout_place_order', function () {
		var $btn = $('#place_order');
		if (!$btn.hasClass('zcs-loading')) {
			$btn.data('zcsLabel', $btn.text()).addClass('zcs-loading').text('প্রক্রিয়াধীন...');
		}
	});

	$body.on('checkout_error', function () {
		var $btn = $('#place_order');
		if ($btn.hasClass('zcs-loading')) {
			$btn.removeClass('zcs-loading').text($btn.data('zcsLabel') || 'অর্ডার নিশ্চিত করুন');
		}
	});

	$body.on('updated_checkout', collapseShipping);

	$(function () {
		syncProvider();
		initSteppers();
		markTotalsLive();
		initNotes();
	});
})(jQuery);
