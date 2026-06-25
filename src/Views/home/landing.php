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
            <p class="text-gray">فقط کافیست یک CNAME به بقچه指向 دهید</p>
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
        <h2>راهنمای اتصال فروشگاه</h2>
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
                <p class="text-gray">یک زیردامنه مانند <code>cart.example.com</code> با رکورد CNAME به سرور بقچه指向 دهید</p>
            </div>
            <div class="step-card">
                <div class="step-number">۳</div>
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
                <div class="step-number">۴</div>
                <h3>انجام شد</h3>
                <p class="text-gray">بقچه به صورت خودکار اطلاعات محصول را از schema.org استخراج می‌کند</p>
            </div>
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

Steps:
1. Find the product page template
2. Add the link with the correct SUBDOMAIN
3. Make sure the page has schema.org/Product markup
4. Style the link as a button matching the site design</pre>
    </div>
</div>
