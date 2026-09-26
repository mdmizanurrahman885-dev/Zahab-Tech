=== Zahab Checkout Style ===

Zahab Tech-এর Cart ও Checkout পেজের জন্য পরিষ্কার, মোবাইল-ফ্রেন্ডলি ডিজাইন।

== ইনস্টল ==

1. WordPress Dashboard → Plugins → Add New → Upload Plugin
2. zahab-checkout-style.zip ফাইলটি বেছে নিয়ে Install Now → Activate

== জরুরি: Checkout ও Cart পেজ "Classic" হতে হবে ==

নতুন WooCommerce-এ Cart/Checkout পেজ Gutenberg Block দিয়ে বানানো থাকে।
এই plugin শুধু Classic (shortcode) পেজে কাজ করে।

1. Pages → Checkout → Edit
2. পেজের সব কনটেন্ট মুছে একটি Shortcode ব্লক দিন: [woocommerce_checkout]
3. Cart পেজে একইভাবে দিন: [woocommerce_cart]
4. Update চাপুন

Elementor দিয়ে এডিট করা পেজ হলে Shortcode widget-এ একই shortcode বসান।

== ডেলিভারি চার্জ (ঢাকার ভেতরে / বাইরে) ==

WooCommerce → Settings → Shipping → Shipping zones থেকে সেট করুন:
- Zone "ঢাকার ভেতরে" (Region: Dhaka) → Flat rate 70
- Zone "ঢাকার বাইরে" (Region: Bangladesh) → Flat rate 120

== কী কী বদলায় ==

- দুই কলাম লেআউট: বামে ফর্ম, ডানে স্টিকি অর্ডার সারসংক্ষেপ (মোবাইলে এক কলাম)
- অপ্রয়োজনীয় ফিল্ড বাদ: Company, Address line 2, Postcode
- বাংলা লেবেল; ইমেইল ও নামের শেষ অংশ ঐচ্ছিক
- পেমেন্ট মেথড কার্ড স্টাইলে, বেছে নেওয়া অপশন হাইলাইট হয়
- "অর্ডার নিশ্চিত করুন" বাটন ও ট্রাস্ট ব্যাজ
- মোবাইলে কার্টের প্রতিটি পণ্য কমপ্যাক্ট কার্ড হিসেবে দেখায়

== বাতিল করতে ==

Plugins → Zahab Checkout Style → Deactivate। সাইট আগের অবস্থায় ফিরে যাবে।
