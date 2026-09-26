(function ($) {
	function sync() {
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

	$(document).on('change', '.zcs-op-radio', sync);
	$(document.body).on('updated_checkout payment_method_selected', sync);
	$(sync);

	$(document).on('click', '.zcs-op-copy', function (e) {
		e.preventDefault();
		var btn = this;
		var text = btn.getAttribute('data-copy');

		function done() {
			btn.textContent = 'কপি হয়েছে';
			btn.classList.add('is-done');
			setTimeout(function () {
				btn.textContent = 'কপি';
				btn.classList.remove('is-done');
			}, 1600);
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
				document.execCommand('copy');
				done();
			} catch (err) {}
			document.body.removeChild(ta);
		}

		if (navigator.clipboard && window.isSecureContext) {
			navigator.clipboard.writeText(text).then(done, fallback);
		} else {
			fallback();
		}
	});
})(jQuery);
