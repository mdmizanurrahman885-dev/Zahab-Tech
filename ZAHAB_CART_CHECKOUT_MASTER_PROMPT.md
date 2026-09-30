# ZAHAB TECH — Cart ও Checkout Plugin বানানোর MASTER PROMPT

> এই ফাইলটা যেকোনো AI (Claude, ChatGPT, Gemini) বা developer কে হুবহু কপি-পেস্ট করে দিলেই একটা সম্পূর্ণ, প্রিমিয়াম, ভুলহীন WooCommerce plugin বানিয়ে দেবে। কোনো follow-up প্রশ্ন করার দরকার হবে না — সব তথ্য নিচে আছে।

---

## 🎯 PROJECT OVERVIEW

**Plugin name:** Zahab Checkout Style  
**Version:** 2.0.0  
**Slug:** `zahab-checkout-style`  
**Purpose:** WooCommerce এর default cart ও checkout পেজকে সম্পূর্ণ redesign করে একটা clean, premium, mobile-first Bangladeshi e-commerce experience বানানো। সাথে একটা "অনলাইন পেমেন্ট" gateway — যেখানে কাস্টমার নিজে bKash/Nagad/Rocket/Upay/CellFin অথবা ব্যাংকে টাকা পাঠিয়ে Transaction ID জমা দেবে।

**Site:** zahabtech.com  
**Theme:** Astra  
**Store type:** Mobile ও tech accessories (smartphone, headphone, tablet, laptop)  
**Target:** বাংলাদেশি গ্রাহক, বেশিরভাগ ঢাকা ও ঢাকার বাইরে  
**Language:** বাংলা primary, English fallback  
**Currency:** BDT (৳)

**Tech constraints:**
- WooCommerce 8+ compatible (HPOS-ready)
- WordPress 6+
- PHP 7.4+
- jQuery অনুমোদিত (WooCommerce এর সাথে আসে)
- Vanilla JS চাই না — jQuery-ই আছে
- Google Fonts CDN allowed (Outfit + Hind Siliguri)
- কোনো external framework না (Bootstrap/Tailwind না) — pure CSS
- Elementor বা page builder এর ওপর dependency চাই না

**Do NOT:**
- Header/footer/menu স্পর্শ করবে না
- Product page স্পর্শ করবে না
- অন্য কোনো plugin এর সাথে conflict বানাবে না
- WooCommerce core template override করবে না — শুধু hooks, filters, ও CSS দিয়ে কাজ করবে

---

## 🎨 DESIGN SYSTEM (CSS variables)

```css
:root {
  /* Brand colors — zahabtech.com এর header logo থেকে নেওয়া */
  --zcs-navy:         #081B4D;  /* Primary — buttons, active state, headings */
  --zcs-navy-2:       #12296A;  /* Primary hover */
  --zcs-teal:         #1D95A8;  /* "TECH" letters, accent tags */
  --zcs-green:        #1FAF55;  /* Success, WhatsApp, "unlocked FREE" */
  --zcs-danger:       #F44541;  /* Error, delete, required asterisk */
  --zcs-badge-red:    #E82F2F;  /* Cart count badge */

  /* Neutrals */
  --zcs-text:         #061527;  /* Body copy */
  --zcs-text-2:       #454449;  /* Labels, secondary */
  --zcs-text-3:       #9A9CA3;  /* Placeholder, table headers, muted */
  --zcs-border:       #E6E9EE;  /* Card borders, dividers */
  --zcs-surface:      #FFFFFF;  /* Card background */
  --zcs-bg:           #F9FBFC;  /* Input background, page tint */
  --zcs-page-bg:      #EEF2FA;  /* Body background (Astra default) */
  --zcs-accent-bg:    #F3F8FA;  /* Selected tile background (light teal-navy) */

  /* MFS brand colors — official */
  --mfs-bkash:        #E2136E;
  --mfs-nagad:        #E8511E;
  --mfs-rocket:       #8C3494;
  --mfs-upay:         #0054A6;
  --mfs-cellfin:      #00843D;

  /* Radius */
  --zcs-r:            10px;   /* Card, big button */
  --zcs-r-sm:         8px;    /* Input, small button, tile */
  --zcs-r-pill:       999px;  /* Chip, badge */

  /* Shadow — subtle only, no drop-shadow */
  --zcs-shadow:       none;
  --zcs-shadow-hover: 0 4px 16px rgba(8, 27, 77, 0.08);
  --zcs-shadow-focus: 0 0 0 3px rgba(8, 27, 77, 0.12);

  /* Typography */
  --zcs-font:         'Outfit', 'Hind Siliguri', system-ui, -apple-system, sans-serif;
  --zcs-font-bn:      'Hind Siliguri', 'Outfit', system-ui, -apple-system, sans-serif;
  --zcs-font-mono:    ui-monospace, 'SF Mono', Consolas, monospace;

  /* Spacing scale */
  --zcs-s-1: 4px;
  --zcs-s-2: 8px;
  --zcs-s-3: 12px;
  --zcs-s-4: 16px;
  --zcs-s-5: 24px;
  --zcs-s-6: 32px;
  --zcs-s-7: 48px;

  /* Transitions */
  --zcs-t-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
  --zcs-t-med:  250ms cubic-bezier(0.4, 0, 0.2, 1);
}
```

**Font loading:**
```
https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Hind+Siliguri:wght@400;500;600;700&display=swap
```

**Font stack rule:**
- English text (numbers, buttons like "PROCEED TO CHECKOUT" — technically English text): **Outfit** primary
- Bangla text (form labels, product names, messages): **Hind Siliguri** primary
- Mixed content: Both fonts loaded, browser picks per glyph
- Never use single font family — always dual stack

---

## 📐 LAYOUT SPECIFICATIONS

### Container width
- Desktop (≥1024px): `max-width: 1184px`, centered, auto margin
- Tablet (768-1023px): `max-width: 100%`, 24px side padding
- Mobile (≤767px): `max-width: 100%`, 16px side padding

### Grid
- **Cart desktop:** 2-column grid `1fr 360px`, gap 28px (form left, totals right)
- **Cart mobile:** single column stack, totals below form
- **Checkout desktop:** 2-column grid `1fr 390px`, gap 28px (form left, order review right)
- **Checkout mobile:** single column, order review at bottom
- **Order review sidebar:** `position: sticky; top: 24px` on desktop

### Breakpoints
- Mobile: 0 - 767px
- Tablet: 768 - 1023px
- Desktop: 1024px+

### Page top gap
- Below site header, before cart/checkout content: **24px on desktop, 12px on mobile**

---

## 🛒 CART PAGE (`[woocommerce_cart]`)

### Above-fold layout (order top → bottom)
1. **"FREE Delivery unlocked!" strip** (green, only if cart total ≥ 1000 tk) — full-width, single line, dismissible-looking but not clickable
2. **Cart form card** (left column, desktop) + **Cart totals card** (right column, sticky)
3. Below on mobile: order stacks

### Cart Form Card (left)
- Background: `#FFFFFF`
- Border: `1px solid #E6E9EE`
- Border-radius: `10px`
- Padding: `24px`
- Contains: Products table, coupon form, update cart button

### Products table (`.shop_table.cart`)
- Width: 100%
- Border: 0 (no visible outer border)
- Row divider: `1px solid #E6E9EE` between rows (not on last row)

#### Table headers (thead th):
- Font: Outfit, 12px, 600 weight, uppercase
- Color: `#9A9CA3`
- Letter-spacing: `0.6px`
- Padding: `14px 8px`
- Text-align: left (except PRICE, SUBTOTAL right)
- Columns: (remove btn) | THUMBNAIL | PRODUCT | PRICE | QUANTITY | SUBTOTAL

#### Product row:
- Row height: min 96px (image 72x72 + padding)
- Thumbnail: `72x72px`, `border-radius: 8px`, `object-fit: contain`, background `#F9FBFC`
- Product name: Outfit, 15px, 600, color `#061527`, hover underline
- Product brand (small line above name): Hind Siliguri, 12px, 500, color `#9A9CA3`
- Price: 15px, 500, right-aligned
- Subtotal: 15px, 600, right-aligned
- Remove (×) button: 
  - Icon: SVG trash (16x16, stroke 2, stroke-linecap round)
  - Color default: `#9A9CA3`
  - Hover: `#F44541`
  - Padding: 8px (touchable)
  - Background: transparent
  - Position: leftmost column, 40px wide

#### Quantity input:
- Layout: minus button | number input | plus button (all inline, no gap)
- Wrapper: `display: flex; align-items: center; border: 1.5px solid #E6E9EE; border-radius: 8px; background: #F9FBFC; overflow: hidden;`
- Buttons: 36x42px, no background, color `#454449`, font 18px, hover bg `#EEF2FA`
- Input: 40x42px, no border, transparent bg, font Outfit 15px 600, text-align center, `type=number` but no spinners (hide with CSS)
- Total wrapper size: `116x42px`

### Coupon form (`.coupon`)
- Display: flex row, gap 10px
- Input `#coupon_code`: 
  - Width: 200px (desktop), 100% (mobile)
  - Height: 47px
  - Padding: `12px 14px`
  - Font: 15px Hind Siliguri
  - Background: `#F9FBFC`
  - Border: `1.5px solid #E6E9EE`
  - Border-radius: 8px
  - Placeholder: "কুপন কোড"
- Apply button:
  - Width: 122px (desktop) / auto (mobile, min 100px)
  - Height: 46px
  - Padding: `12px 20px`
  - Background: `#081B4D`
  - Color: `#FFFFFF`
  - Font: Outfit 14px 600
  - Border-radius: 8px
  - Hover: `#12296A`
  - Label: "কুপন প্রয়োগ"
- Update cart button:
  - Right-aligned on same row (desktop)
  - Or below apply button (mobile)
  - Style: same as apply but background `#FFFFFF`, color `#081B4D`, border `1.5px solid #081B4D`
  - Label: "কার্ট আপডেট"
  - Disabled state: cursor default, opacity 0.5 when nothing to update

### Cart Totals Card (right sidebar, sticky)
- Position: sticky, top 24px on desktop
- Background: `#FFFFFF`
- Border: `1px solid #E6E9EE`
- Border-radius: 10px
- Padding: `24px`
- Width: 360px (desktop)

#### Heading "কার্ট সারসংক্ষেপ"
- Font: Outfit 17px 600, color `#061527`
- Margin-bottom: 20px
- Padding-bottom: 14px
- Border-bottom: `1px solid #E6E9EE`

#### Row layout (subtotal, shipping, total):
- Each row: flex, justify-space-between, padding `10px 0`
- Label (left): 14.5px 500, color `#454449`
- Value (right): 15px 600, color `#061527`

#### Shipping selector:
- Show radio list, each option:
  - Padding: `10px 12px`
  - Border: `1.5px solid #E6E9EE`
  - Border-radius: 8px
  - Margin-bottom: 6px
  - Layout: custom radio dot (14px) + label + right-side price
  - Selected: border `#081B4D`, background `#F3F8FA`
  - Hover: border `#12296A`, background `#F9FBFC`

#### Total row (special):
- Padding-top: 16px
- Border-top: `1.5px solid #E6E9EE`
- Label: Outfit 15px 700, color `#061527`
- Amount: Outfit 20px 700, color `#081B4D`

#### Proceed to checkout button:
- Width: 100%
- Padding: `16px 20px`
- Background: `#081B4D`
- Color: `#FFFFFF`
- Font: Outfit 16px 700 (letter-spacing 0.3px)
- Border-radius: 10px
- Text: "চেকআউট এ যান →"
- Hover: `#12296A`
- Active: transform scale(0.98)
- Focus: outline `3px solid rgba(8, 27, 77, 0.3)`, outline-offset 2px
- Margin-top: 16px

#### Trust badges (below button)
- Display: flex row, gap 18px, wrap, justify-center
- Each item: flex row gap 5px, align-center
- Icon: 13x13 checkmark SVG, color `#1FAF55`
- Text: Hind Siliguri 12.5px 500, color `#9A9CA3`
- Items (3): "৭ দিন রিটার্ন" · "১০০% অরিজিনাল" · "নিরাপদ পেমেন্ট"
- Margin-top: 16px

### Empty cart state
- Center-aligned block, padding 48px 24px
- Icon: cart with 32x32 outline SVG, color `#9A9CA3`
- Heading: "আপনার কার্ট এখনো খালি" (Outfit 20px 600)
- Subtext: "পছন্দের পণ্য খুঁজে যোগ করুন" (Hind Siliguri 14px 400 color `#454449`)
- Button "শপিং শুরু করুন" — same style as proceed button, links to `/shop/`

---

## 💳 CHECKOUT PAGE (`[woocommerce_checkout]`)

### Page structure (top → bottom)
1. **Progress steps** — 3 pill items
2. **Login prompt** (returning customer) — subtle single line
3. **Coupon prompt** — subtle single line
4. **Main grid** — Form left, Order review right
5. **Trust badges** below place-order button

### Progress steps (`.zcs-steps`)
- Container: flex row, gap 14px, justify-center, margin-bottom 22px
- Each step: flex align-center, gap 8px
- Number badge (span): `28x28px`, circle, font Outfit 13px 600
  - Done state: background `#1FAF55`, color white, contents ✓ (14x14 checkmark SVG)
  - Active: background `#081B4D`, color white
  - Future: background `#F3F8FA`, color `#9A9CA3`, border `1.5px solid #E6E9EE`
- Step label: Hind Siliguri 13.5px 500, color:
  - Done: `#1FAF55`
  - Active: `#081B4D`
  - Future: `#9A9CA3`
- Between steps: divider `——` (2px line, 24px wide, color `#E6E9EE`)
- Steps: "কার্ট" (done) · "চেকআউট" (active) · "কনফার্মেশন" (future)

### Login prompt / Coupon prompt
- Subtle blue-tinted strip
- Padding: `12px 16px`
- Background: `#F3F8FA`
- Border-left: `3px solid #081B4D`
- Border-radius: 6px
- Margin-bottom: 12px
- Layout: flex align-center, gap 8px
- Icon: small (person for login, tag for coupon), color `#081B4D`, 16x16
- Text: Hind Siliguri 14px 500, color `#454449`
- Link (clickable part): color `#081B4D`, 14px 600, no underline (underline on hover)
- Text:
  - Login: "পুরনো কাস্টমার? [এখানে লগইন করুন]"
  - Coupon: "কুপন আছে? [এখানে কুপন কোড দিন]"

### Billing form (left column, 716px desktop)
- No card background — just the section, integrated in page
- Heading "বিলিং তথ্য":
  - Font: Outfit 20px 600
  - Color: `#061527`
  - Margin-bottom: 20px
  - Padding-bottom: 12px
  - Border-bottom: `1px solid #E6E9EE`

### Form field layout
- Fields per row on desktop:
  - Row 1: নাম (50%) + নামের শেষ অংশ (50%)
  - Row 2: মোবাইল নম্বর (100%)
  - Row 3: ইমেইল (100%)
  - Row 4: দেশ (100%) — locked to Bangladesh, disabled dropdown
  - Row 5: জেলা (50%) + উপজেলা / থানা (50%)
  - Row 6: সম্পূর্ণ ঠিকানা (100%)
  - Row 7: অর্ডার নোট (100%, textarea, expandable via "নোট যোগ করুন" link)
- On mobile: all rows become full-width, stacked
- Row gap: `margin-bottom: 14px`
- Column gap in 2-col rows: `column-gap: 14px`

### Labels
- Font: Outfit 13px 500
- Color: `#454449`
- Margin-bottom: 6px
- Required indicator: red asterisk `#F44541` beside label, not after
- Optional indicator: gray "(ঐচ্ছিক)" after label in `#9A9CA3` 12px 400

### Inputs / textareas / selects
- Height: 48px (input/select), auto (textarea min 96px)
- Padding: `12px 14px` (input) / `12px 14px` (textarea)
- Background: `#F9FBFC`
- Border: `1.5px solid #E6E9EE`
- Border-radius: 8px
- Font: Hind Siliguri 15px 400, color `#061527`
- Placeholder color: `#9A9CA3`
- Focus state:
  - Background: `#FFFFFF`
  - Border-color: `#081B4D`
  - Outline: none
  - Box-shadow: `0 0 0 3px rgba(8, 27, 77, 0.12)`
- Error state:
  - Border-color: `#F44541`
  - Background: `#FEF2F2`
- Error message: below field, Hind Siliguri 13px 500, color `#F44541`, margin-top 4px, prefix icon ⚠

### Field placeholders (all bangla):
- নাম: "যেমন: রহিম উদ্দিন"
- নামের শেষ অংশ: "ঐচ্ছিক"
- মোবাইল নম্বর: "01XXXXXXXXX"
- ইমেইল: "ঐচ্ছিক — অর্ডার আপডেট পেতে"
- জেলা: dropdown, default "জেলা বেছে নিন"
- উপজেলা / থানা: "যেমন: সাভার"
- সম্পূর্ণ ঠিকানা: "বাড়ি নং, রোড, এলাকা"
- অর্ডার নোট: "বিশেষ কোনো নির্দেশনা থাকলে লিখুন (ঐচ্ছিক)"

### Order review sidebar (right column, 390px desktop)
- Background: `#FFFFFF`
- Border: `1px solid #E6E9EE`
- Border-radius: 10px
- Padding: `24px`
- Position: sticky, top 24px (desktop)

#### "অর্ডার সারসংক্ষেপ" heading
- Font: Outfit 17px 600
- Margin-bottom: 20px
- Padding-bottom: 14px
- Border-bottom: `1px solid #E6E9EE`

#### Product rows (in order review)
- Each row: flex, padding `10px 0`, border-bottom `1px dashed #E6E9EE`
- Layout: thumbnail (48x48, radius 6px) + name + qty × price
- Name: 14px 500 color `#061527`, line-clamp 2
- Right side: "৳ 23,990 × 1" or just amount if qty=1, 14px 600, color `#061527`

#### Subtotal / Shipping / Total rows
- Same pattern as cart totals
- Shipment section: shows selected shipping option only (chip style, not radio here — already picked in cart)
- Total row: highlighted, background `#F3F8FA`, padding `12px 16px`, margin-left/right `-16px`, border-radius 8px
- Total amount font: Outfit 22px 700, color `#081B4D`

#### Payment section
- Heading "পেমেন্ট পদ্ধতি":
  - Font: Outfit 15px 600
  - Margin: 24px 0 12px
- Payment method tiles (radio list): see Payment section below
- After tile: place order button

#### Place order button
- Same style as cart's proceed button but:
- Text: "অর্ডার নিশ্চিত করুন"
- Loading state: spinner + "প্রক্রিয়াধীন..."
- Success (rare, before redirect): checkmark + "সফল!"
- Disabled: opacity 0.5, cursor not-allowed

#### Trust badges below button
- Same as cart

---

## 💰 PAYMENT GATEWAY: "অনলাইন পেমেন্ট"

**Gateway ID:** `zcs_online`  
**Class name:** `ZCS_Gateway_Online extends WC_Payment_Gateway`

### How it works (customer flow)
1. Customer sees 2 groups of tiles:
   - **মোবাইল ব্যাংকিং:** bKash, Nagad, Rocket, Upay, CellFin (only shown if numbers are configured)
   - **ব্যাংক ট্রান্সফার:** IBBL, DBBL, EBL (each with account details)
2. Customer picks one tile
3. Panel below tile shows:
   - MFS: List of numbers with "COPY" button, description tag (personal/agent, send money/cash out)
   - Bank: Account name, number, branch, routing — each copyable
4. Customer sends money via their own app/branch
5. Customer types "পাঠানো নম্বর" and "Transaction ID" below
6. On submit → validation → order status: **On hold**, TrxID saved as meta

### Payment method tile (`li.wc_payment_method`)
- Container: `padding: 14px; border: 1.5px solid #E6E9EE; border-radius: 10px; background: #FFFFFF; margin-bottom: 10px`
- Selected: `border-color: #081B4D; background: #F3F8FA`
- Label: flex row, gap 12px, align-center
- Icon: 32x32 (bank icon or MFS logo)
- Title: Outfit 15px 600, color `#061527`
- Subtitle: Hind Siliguri 13px 400, color `#454449`

### MFS tiles (inside "অনলাইন পেমেন্ট" panel)
- Layout: grid 3 columns on desktop, 2 on mobile, gap 8px
- Each tile:
  - Padding: 12px 8px
  - Background: `#F9FBFC`
  - Border: `1.5px solid #E6E9EE`
  - Border-radius: 8px
  - Text-align: center
  - Cursor: pointer
- Selected tile: border `#081B4D` (2px), background `#F3F8FA`, `--brand` variable applied
- Brand color stripe on top: 3px, uses `--brand` (bKash pink, Nagad orange, etc.)
- Label: Outfit 14px 600, color `#061527`
- Radio input: visually hidden, screen-reader accessible

### Number display list (inside selected tile panel)
- Each row:
  - Padding: 12px 14px
  - Background: `#FFFFFF`
  - Border: `1px solid #E6E9EE`
  - Border-radius: 8px
  - Margin-bottom: 6px
  - Layout: flex justify-space-between align-center
- Number: font-mono 15px 600 color `#061527`
- Tag: Hind Siliguri 12px 500 color `#454449`, padding `2px 8px`, background `#F3F8FA`, border-radius pill
- Copy button: 
  - 60x30px
  - Background: `#FFFFFF`
  - Border: `1.5px solid #081B4D`
  - Color: `#081B4D`
  - Font: Outfit 12px 600
  - Border-radius: 6px
  - Hover: bg `#081B4D`, color white
  - Success state (2 sec): bg `#1FAF55`, color white, text "কপি হয়েছে"

### Bank card layout
- Each field: flex row, padding `8px 0`, border-bottom `1px dashed #E6E9EE`
- Label (left): 13px 500, color `#454449`, width 40%
- Value (right): 15px 600, color `#061527`, right-aligned or flex-1
- Copyable fields (account number, routing): shown as monospace with copy button

### Sender + TrxID input row
- Below all tiles
- 2 form rows:
  - Sender: label "যে নম্বর থেকে পাঠিয়েছেন *" (for MFS) or "যে অ্যাকাউন্ট থেকে পাঠিয়েছেন (নাম বা নম্বর) *" (for bank)
    - Input placeholder: "01XXXXXXXXX" or "যেমন: Rahim, A/C শেষ ৪ ডিজিট 1234"
    - inputmode: "tel" for MFS, "text" for bank
  - TrxID: label "Transaction ID *" (or "Transaction / Reference ID *" for bank)
    - Placeholder: "যেমন: 9K7A2B4XQ1"
    - autocapitalize: "characters"
    - Font family: monospace
    - Uppercase visual on typing (via CSS `text-transform: uppercase`)
- Both inputs full-width, same styles as billing form inputs

### Charge note (below inputs)
- Padding: 12px 14px
- Background: `#FEF9E7`
- Border-left: `3px solid #F5B800`
- Border-radius: 6px
- Font: Hind Siliguri 13px 500, color `#5C4A00`
- Icon: ⓘ 14x14 warning
- Default text: "রেগুলার চার্জ প্রযোজ্য। চার্জসহ টাকা পাঠান।" (editable in admin)

---

## ✅ VALIDATION RULES

### On submit (`validate_fields()`)

#### Provider (required, must exist)
- Error: "কোন মাধ্যমে টাকা পাঠিয়েছেন সেটি বেছে নিন।"

#### Sender (required)
- If MFS provider:
  - Strip non-digits
  - Remove leading "88" if present
  - Must match: `/^01[3-9]\d{8,9}$/`
  - Error: "যে নম্বর থেকে টাকা পাঠিয়েছেন সেটি সঠিকভাবে লিখুন (যেমন: 01712345678)।"
- If bank provider:
  - Min length 3 characters
  - Error: "যে অ্যাকাউন্ট থেকে টাকা পাঠিয়েছেন তার নাম বা নম্বর লিখুন।"

#### TrxID (required)
- Strip whitespace, uppercase
- Must match: `/^[A-Z0-9\-]{6,30}$/`
- Error: "সঠিক Transaction ID লিখুন। এতে শুধু ইংরেজি অক্ষর ও সংখ্যা থাকে (৬ থেকে ৩০ অক্ষর)।"
- Must be unique across all orders (check `_zcs_trx` meta):
  - Error: "এই Transaction ID দিয়ে আগেই একটি অর্ডার হয়েছে। সঠিক ID দিন অথবা আমাদের WhatsApp-এ যোগাযোগ করুন।"

### On process_payment()
- Save meta: `_zcs_provider`, `_zcs_sender`, `_zcs_trx`
- Set order transaction ID = TrxID
- Update order status → `on-hold`
- Order note: "{provider} পেমেন্ট যাচাই বাকি। প্রেরক: {sender}, Transaction ID: {trx}"
- Reduce stock
- Empty cart
- Return success + redirect to thank-you page

### Billing fields validation (built into WooCommerce)
- Required fields: নাম, মোবাইল নম্বর, দেশ (locked BD), জেলা, উপজেলা/থানা, সম্পূর্ণ ঠিকানা
- Optional: নামের শেষ অংশ, ইমেইল, অর্ডার নোট
- Phone regex: `/^(?:\+88)?01[3-9]\d{8,9}$/` (Bangladesh mobile)

---

## 📋 DEFAULT DATA (Zahab Tech specific — pre-fill on activation)

### bKash numbers
```
01750646599 | পার্সোনাল · Send Money
01650020096 | পার্সোনাল · Send Money
01865441898 | পার্সোনাল · Send Money
01843135010 | এজেন্ট · Cash Out
```

### Nagad numbers
```
01750646599 | পার্সোনাল · Send Money
01865441898 | পার্সোনাল · Send Money
```

### Rocket numbers (12 digits!)
```
017506465999 | পার্সোনাল · Send Money
```

### Upay
```
(empty by default — admin fills if needed)
```

### CellFin
```
01750646599 | Send Money
```

### Banks
Format: `SHORT | FullName | HolderName | AccountNumber | Branch | Routing`
```
IBBL | Islami Bank Bangladesh PLC | MD. MIZANUR RAHMAN | 20501300205501710 | Savar | 125263914
DBBL | Dutch-Bangla Bank PLC | MD. MIZANUR RAHMAN | 1371570429928 | Savar | 090263881
EBL  | Eastern Bank PLC | MD. MIZANUR RAHMAN | 1701440031938 | Savar | 085263889
```

### Trust badges (3 items, order preserved)
1. ৭ দিন রিটার্ন
2. ১০০% অরিজিনাল
3. নিরাপদ পেমেন্ট

### Charge note default
```
রেগুলার চার্জ প্রযোজ্য। চার্জসহ টাকা পাঠান।
```

### Shipping (managed by WooCommerce zone settings, plugin doesn't touch — but should display cleanly)
- Inside Dhaka Delivery: ৳80
- Outside Dhaka Delivery: ৳120
- Free Delivery: min amount ৳1000

---

## 🔧 ADMIN EXPERIENCE

### Gateway settings page (WooCommerce → Settings → Payments → অনলাইন পেমেন্ট → Manage)
Fields (in order):
1. **চালু / বন্ধ** (checkbox, default: enabled)
2. **চেকআউটে নাম** (text, default: "অনলাইন পেমেন্ট")
3. **ছোট বিবরণ** (textarea, default: empty)
4. **bKash নম্বর** (textarea, help text explains "প্রতি লাইনে একটি নম্বর: নম্বর | বিবরণ")
5. **Nagad নম্বর** (same)
6. **Rocket নম্বর** (same + note "Rocket নম্বর ১২ ডিজিটের")
7. **Upay নম্বর** (same)
8. **CellFin নম্বর** (same)
9. **ব্যাংক অ্যাকাউন্ট** (textarea, larger, help text explains bank line format)
10. **চার্জ সংক্রান্ত নোট** (text)

Help text under each field visible in admin UI (not hidden).

### Order display

#### Orders table (`shop_order` list)
- Add new column after "Order status": **"পেমেন্ট / TrxID"**
- Column shows:
  - Provider name (bKash / Nagad / bank name)
  - Below: TrxID in `<code>` tag, monospace
  - If not zcs_online: show "—"

#### Order detail page (edit-order screen)
- After billing address, show custom box:
  - Heading: "অনলাইন পেমেন্ট তথ্য"
  - Background: `#F6F8FB`
  - Border: `1px solid #E3E8EF`
  - Border-radius: 6px
  - Padding: `10px 12px`
  - Rows:
    - **মাধ্যম:** {provider}
    - **প্রেরক:** {sender}
    - **Transaction ID:** {trx} (monospace, 13px)
  - Footer note: "আপনার অ্যাপের লেনদেনের তালিকায় এই ID মিলিয়ে অর্ডারটি Processing করুন।"

#### Order totals footer (customer thank-you page + email)
- Add extra row via `woocommerce_get_order_item_totals` filter:
  - Label: "পেমেন্ট তথ্য:"
  - Value: "{provider} · {sender} · TrxID: {trx}"

---

## 📱 MOBILE-SPECIFIC RULES (≤767px)

- Container padding: 16px (not 24px)
- Product image in cart: 60x60 (not 72x72)
- Cart form card padding: 16px (not 24px)
- Cart totals sidebar: full-width below form, remove sticky
- Order review sidebar: full-width below form
- Form 2-column rows collapse to single column
- Font sizes: all inputs 16px minimum (prevent iOS zoom)
- Buttons: full-width where applicable
- MFS tile grid: 2 columns instead of 3
- Payment tile padding: 12px 10px (compact)
- Progress steps: reduce gap to 8px, may hide dividers on very small screens
- Sticky "Place order" bar on mobile (optional enhancement):
  - Position: fixed bottom
  - Background: white with `box-shadow: 0 -4px 20px rgba(0,0,0,0.06)`
  - Padding: 12px 16px
  - Shows: Total amount left, "অর্ডার নিশ্চিত করুন" button right
  - Only appears after user scrolls past order review

---

## ♿ ACCESSIBILITY

- All inputs have `<label>` (proper `for` + `id`)
- Required fields: `aria-required="true"` + visible asterisk
- Error messages: `role="alert"` + linked via `aria-describedby`
- Custom radios (payment tiles, shipping options): use hidden native radio + label — keyboard-navigable
- Focus visible: outline on all interactive elements (never `outline: none` without alternative)
- Color contrast:
  - Body text on white: 12:1+ ✓
  - Muted text `#9A9CA3` on white: minimum for hint text only (>4:1)
  - Buttons `#081B4D` bg / white text: 15:1 ✓
- Live regions:
  - Cart totals update: `aria-live="polite"`
  - Error notices: `role="alert"`
- Skip link at top of page: "Skip to order form" (screen-reader only)

---

## 🚀 PERFORMANCE

- Total CSS file size: **≤ 40KB minified, gzipped ≤ 10KB**
- No Google Fonts blocking render — use `display=swap`
- Prefetch fonts: `<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>`
- No jQuery for cart/checkout beyond what WooCommerce loads
- No polling/setInterval (except one 200ms safety check for gateway sync)
- Enqueue CSS/JS only on cart + checkout pages (check `is_cart() || is_checkout()`)
- Use `wp_enqueue_style` version = plugin version (for cache busting)

---

## 🏗️ FILE STRUCTURE

```
zahab-checkout-style/
├── zahab-checkout-style.php      # Main plugin file, hooks, filters
├── includes/
│   ├── class-zcs-gateway.php     # Payment gateway class
│   └── class-zcs-fields.php      # Billing field customization
├── assets/
│   ├── zahab-checkout.css        # All styles (single file)
│   └── zahab-checkout.js         # Tile switching, copy, sender/trx UI
├── readme.txt                     # WordPress plugin readme
└── uninstall.php                  # Optional: cleanup on uninstall
```

**Plugin header:**
```php
/**
 * Plugin Name: Zahab Checkout Style
 * Description: Zahab Tech-এর জন্য পরিষ্কার, মোবাইল-ফ্রেন্ডলি Cart ও Checkout পেজ ডিজাইন — সাথে "অনলাইন পেমেন্ট" gateway।
 * Version:     2.0.0
 * Author:      ZAHAB TECH
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * WC requires at least: 8.0
 * Requires Plugins: woocommerce
 * Text Domain: zahab-checkout-style
 */
```

**HPOS compatibility declaration** (required for WC 8+):
```php
add_action('before_woocommerce_init', function () {
    if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
    }
});
```

---

## 🧪 TESTING CHECKLIST

### Functional tests
- [ ] Add product to cart → cart shows correct total
- [ ] Quantity + / - buttons update total (via AJAX, no reload)
- [ ] Remove product → cart updates
- [ ] Apply valid coupon → discount shown
- [ ] Apply invalid coupon → error message
- [ ] Free delivery unlocks at ৳1000+
- [ ] Proceed to checkout → all cart items carried
- [ ] Billing form: try to submit empty → each required field shows Bangla error
- [ ] Enter invalid Bangladesh phone → error
- [ ] Select payment tile → panel switches instantly (no reload)
- [ ] Copy number → button flashes "কপি হয়েছে"
- [ ] Submit with wrong TrxID format → error
- [ ] Submit with duplicate TrxID → error
- [ ] Submit valid → order created, status "On hold", thank-you page loads
- [ ] Admin sees new column in Orders list with provider + TrxID
- [ ] Admin sees payment info box in order detail
- [ ] Change status to "Processing" → OK

### Visual tests
- [ ] Desktop 1440px — layout looks clean, sidebar sticky
- [ ] Desktop 1024px — no horizontal scroll
- [ ] Tablet 768px — layout transitions smoothly
- [ ] Mobile 375px — no horizontal scroll, all buttons tappable (min 44x44px)
- [ ] iPhone SE 320px — degrades gracefully
- [ ] Focus visible on all inputs & buttons
- [ ] Placeholders don't overlap text
- [ ] Long product names wrap correctly
- [ ] Very long TrxID (max 30 chars) fits input

### Browser tests
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (iOS 15+)
- [ ] Samsung Internet
- [ ] Edge

---

## 🎯 SUCCESS CRITERIA (Definition of Done)

A perfect result matches ALL of these:

1. **Zero horizontal scroll** on any device from 320px to 1920px
2. **No layout shift** on load (measure CLS: < 0.05)
3. **First interactive** < 1.5s on 4G (LCP < 2s)
4. **Bangla text renders** correctly (no boxes, no fallback English rendering)
5. **All error messages in Bangla** — not a single English error visible to customer
6. **Numbers copy** with one tap, feedback visible
7. **Tile switch** feels instant (no delay, no flash)
8. **Sticky sidebar** on desktop doesn't overlap footer
9. **No console errors** in browser DevTools
10. **Order data preserved** — TrxID, sender, provider all appear in admin & email
11. **Uninstall clean** — removing plugin restores default WooCommerce cart/checkout
12. **Cache-safe** — LiteSpeed/WP Rocket doesn't break dynamic content
13. **Colors match** brand navy `#081B4D` throughout
14. **Fonts load** without FOIT (flash of invisible text)
15. **Form submits** even if JavaScript disabled (progressive enhancement)

---

## 📞 IF ANYTHING BREAKS

Fallback plan built into plugin:
- Deactivating plugin → site instantly returns to default WooCommerce cart/checkout
- No database migrations (uses standard `_zcs_*` post meta)
- No dependency on other plugins beyond WooCommerce core

---

## 🎨 REFERENCE INSPIRATION (Don't copy, but understand)

- **Sumash Tech** (sumashtech.com) — Bengali labels, MFS integration style
- **Star Tech** (startech.com.bd) — cart-checkout flow density
- **Daraz Bangladesh** — mobile-first form patterns
- **Shopify default checkout** — spacing rhythm, trust badges placement
- **Amazon India** — sticky order summary

But the final result should be **cleaner and more elegant than all of them** — no clutter, no upsells, no distractions, just fast checkout.

---

## 📝 FINAL NOTES FOR THE AI/DEVELOPER BUILDING THIS

1. **Bangla-first**: If English and Bangla labels conflict anywhere, Bangla wins.
2. **Minimal is premium**: Fewer borders, less color, more whitespace = more premium feel.
3. **Never break WooCommerce hooks**: Every WC action must be preserved so 3rd-party plugins (analytics, shipping calculators) still work.
4. **Don't reinvent**: Use WooCommerce's built-in checkout flow via `[woocommerce_checkout]` shortcode — just style + customize via filters.
5. **Test with real Bangladesh phone numbers**: 013, 014, 015, 016, 017, 018, 019 — all should validate.
6. **Comment code in English** but user-facing strings in Bengali.
7. **Version 2.0.0** — this is a full rebuild, not incremental.

---

## END OF MASTER PROMPT

*এই ফাইলটা যেকোনো AI চ্যাটে দিয়ে বলবেন: "উপরের master prompt ব্যবহার করে একটা সম্পূর্ণ WordPress plugin বানাও। আমাকে zip আকারে দাও।"*

*তারা সরাসরি কাজে নেমে যাবে, কোনো প্রশ্ন করবে না, সব তথ্যই এখানে আছে।*
