=== Zahab Checkout Style ===
Contributors: zahabtech
Tags: woocommerce, checkout, cart, bkash, nagad
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
WC requires at least: 8.0
Stable tag: 2.0.0
License: GPLv2 or later

Zahab Tech-এর Cart ও Checkout পেজের পরিষ্কার, মোবাইল-ফ্রেন্ডলি ডিজাইন, সাথে bKash / Nagad / Rocket / Upay / CellFin / ব্যাংক "অনলাইন পেমেন্ট"।

== Description ==

* Cart: পণ্যের কার্ড, +/− পরিমাণ বাটন (নিজে আপডেট হয়), কুপন, স্টিকি কার্ট সারসংক্ষেপ, ফ্রি ডেলিভারি নোটিস, খালি কার্টের পেজ
* Checkout: ধাপ নির্দেশক, বাংলা ফর্ম, দেশ বাংলাদেশে লক, বাংলাদেশি মোবাইল নম্বর যাচাই, স্টিকি অর্ডার সারসংক্ষেপ
* অনলাইন পেমেন্ট: নম্বর কপি বাটন, প্রেরকের নম্বর ও Transaction ID, একই TrxID দুবার ব্যবহার করা যায় না
* অর্ডার "On hold" অবস্থায় আসে; Orders তালিকায় "পেমেন্ট / TrxID" কলাম
* শুধু Cart ও Checkout পেজের মূল অংশে কাজ করে। Header, Footer, মেনু, প্রোডাক্ট পেজ বদলায় না
* WooCommerce template override করে না; শুধু hooks, filters ও CSS

== Installation ==

1. Plugins → Add New Plugin → Upload Plugin → zahab-checkout-style.zip → Install Now → Activate
2. Cart পেজে [woocommerce_cart] এবং Checkout পেজে [woocommerce_checkout] shortcode রাখুন (Block দিয়ে বানানো পেজে কাজ করবে না)
3. WooCommerce → Settings → Payments → "অনলাইন পেমেন্ট" চালু করুন, নম্বর মিলিয়ে নিন
4. পুরনো bKash/Nagad plugin ও Direct bank transfer বন্ধ করুন
5. WooCommerce → Settings → Shipping:
   Inside Dhaka Delivery 80, Outside Dhaka Delivery 120,
   Free shipping (Minimum order amount 1000) চাইলে যোগ করুন
6. Cache plugin থাকলে Purge All

== Frequently Asked Questions ==

= ফ্রি ডেলিভারি নোটিস কখন দেখায়? =
যখন কার্টে সত্যিই Free shipping অপশন পাওয়া যায়। Shipping zone-এ Free shipping method যোগ না করলে নোটিস দেখাবে না।

= Plugin বন্ধ করলে কী হবে? =
Cart ও Checkout সাথে সাথে WooCommerce-এর আগের চেহারায় ফিরে যাবে। আগের অর্ডারের পেমেন্ট তথ্য থেকে যাবে।

== Changelog ==

= 2.0.0 =
* সম্পূর্ণ নতুন ডিজাইন সিস্টেম
* +/− পরিমাণ বাটন, ব্র্যান্ড লাইন, খালি কার্ট পেজ, ফ্রি ডেলিভারি নোটিস
* Checkout: ধাপ নির্দেশক, দেশ লক, মোবাইল নম্বর যাচাই, অর্ডার নোট লিংকের পেছনে
* পেমেন্ট টাইলে ব্র্যান্ড রঙের দাগ, নম্বর কার্ড, হলুদ চার্জ নোট
* সব গ্রাহক-দেখা বার্তা বাংলায়
* uninstall.php, minified CSS

= 1.1.2 =
* শুধু পেজের মূল অংশে স্টাইল সীমিত
