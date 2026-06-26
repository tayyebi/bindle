<div class="landing">
    <div class="landing-hero">
        <img src="/img/logo.svg" alt="<?= trans('app.name') ?>" width="80" height="80">
        <h1><?= trans('app.name') ?></h1>
        <p class="text-gray">مدیریت سبد خرید و پرداخت برای فروشگاه‌های آنلاین</p>
        <div class="landing-actions">
            <a href="/login" class="btn btn-primary"><?= trans('login') ?></a>
        </div>
    </div>

    <div class="landing-features">
        <div class="feature-card">
            <h3>اتصال ساده</h3>
            <p class="text-gray">فقط کافیست یک CNAME به بقچه اشاره کنید</p>
        </div>
        <div class="feature-card">
            <h3>تشخیص خودکار محصول</h3>
            <p class="text-gray">محصولات را از schema.org به صورت خودکار تشخیص می‌دهد</p>
        </div>
        <div class="feature-card">
            <h3>پرداخت دستی</h3>
            <p class="text-gray">تأیید پرداخت با اسکرین‌شات یا شماره تراکنش</p>
        </div>
    </div>

    <div class="landing-section">
        <h2>راهنمای اتصال فروشگاه (On-Boarding)</h2>
        <p class="text-gray">برای افزودن سبد خرید و پرداخت به فروشگاه خود، مراحل زیر را انجام دهید:</p>
        <div class="landing-steps">
            <div class="step-card">
                <div class="step-number">۱</div>
                <h3>ثبت فروشگاه</h3>
                <p class="text-gray">ادمین سیستم فروشگاه شما را در پنل مدیریت ایجاد می‌کند</p>
            </div>
            <div class="step-card">
                <div class="step-number">۲</div>
                <h3>تنظیم DNS</h3>
                <p class="text-gray">یک زیردامنه مانند <code>cart.example.com</code> با رکورد CNAME به سرور بقچه اشاره کنید</p>
            </div>
            <div class="step-card">
                <div class="step-number">۳</div>
                <h3>تنظیم هدرها (Headers)</h3>
                <p class="text-gray">برای تشخیص خودکار محصول، هدرهای زیر را در سرور خود تنظیم کنید:</p>
                <pre class="code-sample dir-ltr">Content-Type: text/html; charset=utf-8
X-Robots-Tag: all
Access-Control-Allow-Origin: *</pre>
                <p class="text-gray" style="margin-top:0.5rem;font-size:0.85rem;">
                    هدر <code>Content-Type</code> با charset=utf-8 برای خواندن صحیح کاراکترهای فارسی ضروری است.
                </p>
            </div>
            <div class="step-card">
                <div class="step-number">۴</div>
                <h3>افزودن لینک</h3>
                <p class="text-gray">لینک زیر را در صفحه محصول خود قرار دهید:</p>
                <pre class="code-sample">&lt;a href="https://SUBDOMAIN/add?url=PRODUCT_URL"&gt;
  افزودن به سبد خرید
&lt;/a&gt;</pre>
                <p class="text-gray" style="margin-top:0.75rem;">اگر URL شامل کاراکترهای خاص است از روش base64 استفاده کنید:</p>
                <pre class="code-sample">&lt;a href="https://SUBDOMAIN/add?b64=BASE64_URL"&gt;
  افزودن به سبد خرید
&lt;/a&gt;</pre>
            </div>
            <div class="step-card">
                <div class="step-number">۵</div>
                <h3>بررسی ساختار داده (Schema.org)</h3>
                <p class="text-gray">بقچه از ساختارهای استاندارد schema.org پشتیبانی می‌کند. صفحه محصول شما باید شامل JSON-LD یا Microdata باشد:</p>
                <div style="text-align:right;font-size:0.85rem;line-height:1.8;">
                    <strong>انواع پشتیبانی شده:</strong>
                    <ul style="margin:0.5rem 0;padding-right:1.5rem;">
                        <li><code>schema.org/Product</code></li>
                        <li><code>schema.org/Offer</code> (price, priceCurrency, inventoryLevel)</li>
                        <li><code>schema.org/AggregateOffer</code> (highPrice, lowPrice, priceCurrency)</li>
                    </ul>
                    <strong>پراپرتی‌های ضروری:</strong>
                    <ul style="margin:0.5rem 0;padding-right:1.5rem;">
                        <li><code>name</code> — نام محصول</li>
                        <li><code>offers.price</code> — قیمت (عددی مثبت)</li>
                    </ul>
                    <strong>پراپرتی‌های اختیاری:</strong>
                    <ul style="margin:0.5rem 0;padding-right:1.5rem;">
                        <li><code>description</code> — توضیحات</li>
                        <li><code>image</code> — URL تصویر</li>
                        <li><code>offers.priceCurrency</code> — واحد پول (پیش‌فرض: USD)</li>
                        <li><code>offers.inventoryLevel.value</code> — موجودی انبار (عدد صحیح)</li>
                        <li><code>productType</code> — نوع محصول (physical/digital)</li>
                    </ul>
                </div>
                <p class="text-gray" style="margin-top:0.5rem;font-size:0.85rem;">
                    <strong>مثال inventoryLevel:</strong> اگر موجودی انبار را تعریف کنید، بقچه آن را ذخیره می‌کند.
                </p>
                <pre class="code-sample dir-ltr">{
  "@type": "Offer",
  "price": 250000,
  "priceCurrency": "IRR",
  "inventoryLevel": {
    "@type": "QuantitativeValue",
    "value": 42
  }
}</pre>
            </div>
            <div class="step-card">
                <div class="step-number">۶</div>
                <h3>انجام شد</h3>
                <p class="text-gray">بقچه به صورت خودکار اطلاعات محصول را از schema.org استخراج می‌کند</p>
            </div>
        </div>
        <p class="text-gray" style="margin-top:1.5rem;font-size:0.9rem;">
            <strong>توجه:</strong> crawler بقچه با User-Agent <code>BindleBot/1.0</code> صفحه محصول شما را برای استخراج اطلاعات بررسی می‌کند.
            در صورت استفاده از CDN یا فایروال، این crawler را مجاز کنید.
        </p>
        </div>
    </div>

    <div class="landing-section">
        <h2>نمونه پرامپت برای AI Agent</h2>
        <p class="text-gray">برای ادغام سریع وب‌سایت با بقچه، این پرامپت را در AI Agent خود (Cursor، Claude، ChatGPT) کپی کنید:</p>
        <pre class="code-sample code-block" dir="ltr">You are integrating a website with Bindle (بقچه), a shopping cart &amp; payment SaaS.

The merchant has a CNAME subdomain pointing to Bindle (e.g. cart.example.com).

On each product detail page, add an "add to cart" link:

&lt;a href="https://SUBDOMAIN/add?url=PRODUCT_URL"&gt;
  Add to Cart
&lt;/a&gt;

- SUBDOMAIN: the merchant's subdomain (e.g. cart.example.com)
- PRODUCT_URL: URL-encoded full URL of the product page

For complex URLs use base64 instead:

&lt;a href="https://SUBDOMAIN/add?b64=BASE64_URL"&gt;
  Add to Cart
&lt;/a&gt;

- BASE64_URL: standard base64 of the full product URL

Header requirements:
- Set Content-Type: text/html; charset=utf-8
- Set X-Robots-Tag: all
- Set Access-Control-Allow-Origin: *

Supported schema.org markup (JSON-LD or Microdata):
- Product (name, description, image)
- Offer (price, priceCurrency, inventoryLevel.value)
- AggregateOffer (highPrice, lowPrice)

Steps:
1. Find the product page template
2. Add the link with the correct SUBDOMAIN
3. Add schema.org/Product JSON-LD to the page head (if missing)
4. Include offers.inventoryLevel.value for stock tracking (optional)
5. Style the link as a button matching the site design
6. Whitelist BindleBot/1.0 in CDN/firewall so Bindle can fetch product data</pre>
    </div>
</div>
