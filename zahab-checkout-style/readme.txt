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
যে zone-এ "Inside Dhaka Delivery" ও "Outside Dhaka Delivery" আছে সেটি খুলুন:
- Inside Dhaka Delivery → Edit → Cost: 80
- Outside Dhaka Delivery → Edit → Cost: 120
- Save changes

== কী কী বদলায় ==

শুধু Cart ও Checkout পেজের মূল অংশ (#content)। Header, Footer, মেনু, mini-cart বদলায় না।


- দুই কলাম লেআউট: বামে ফর্ম, ডানে স্টিকি অর্ডার সারসংক্ষেপ (মোবাইলে এক কলাম)
- অপ্রয়োজনীয় ফিল্ড বাদ: Company, Address line 2, Postcode
- বাংলা লেবেল; ইমেইল ও নামের শেষ অংশ ঐচ্ছিক
- পেমেন্ট মেথড কার্ড স্টাইলে, বেছে নেওয়া অপশন হাইলাইট হয়
- "অর্ডার নিশ্চিত করুন" বাটন ও ট্রাস্ট ব্যাজ
- মোবাইলে কার্টের প্রতিটি পণ্য কমপ্যাক্ট কার্ড হিসেবে দেখায়

== অনলাইন পেমেন্ট (bKash / Nagad / Rocket / CellFin / ব্যাংক) ==

WooCommerce → Settings → Payments → "অনলাইন পেমেন্ট" → Manage
- এখানে নম্বর ও ব্যাংক অ্যাকাউন্ট বদলানো, যোগ করা বা মুছে ফেলা যায়।
- পুরনো bKash/Nagad plugin এবং "Direct bank transfer" বন্ধ করে দিন, না হলে চেকআউটে দুবার দেখাবে।

গ্রাহকের দেওয়া তথ্য কোথায় পাবেন:
- WooCommerce → Orders: "পেমেন্ট / TrxID" কলামে মাধ্যম ও Transaction ID
- অর্ডার খুললে Billing ঠিকানার নিচে "অনলাইন পেমেন্ট তথ্য" বক্স
- নতুন অর্ডারের ইমেইলেও এই তথ্য থাকে
অর্ডার "On hold" অবস্থায় আসে। অ্যাপে টাকা মিলিয়ে "Processing" করুন।
একই Transaction ID দিয়ে দ্বিতীয়বার অর্ডার করা যায় না।

== বাতিল করতে ==

Plugins → Zahab Checkout Style → Deactivate। সাইট আগের অবস্থায় ফিরে যাবে।
