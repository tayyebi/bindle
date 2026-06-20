# بقچه (Bindle)

مدیریت سبد خرید و پرداخت دستی برای فروشگاه‌های آنلاین.

## نمای کلی

یک SaaS چندمستاجره که فروشندگان با اشاره CNAME یک زیردامنه به بقچه، سبد خرید و پرداخت را به فروشگاه خود اضافه می‌کنند.

## ویژگی‌ها

- **تشخیص خودکار محصول**: با parsing schema.org/Product از صفحه محصول
- **سبد خرید مبتنی بر کوکی + دیتابیس**: ماندگار و قابل ارجاع
- **تسویه حساب میزبانی شده**: فرم تسویه روی دامنه فروشگاه
- **پرداخت دستی**: تأیید با اسکرین‌شات یا شماره تراکنش
- **داشبورد فروشنده**: مدیریت سفارش‌ها، تأیید/رد پرداخت‌ها
- **وب‌هوک**: ارسال رویدادهای سفارش به آدرس دلخواه
- **تحویل محصولات دیجیتال**: لینک دانلود پس از تأیید پرداخت
- **مدیریت سیستم**: پنل ادمین برای مدیریت فروشگاه‌ها

## تکنولوژی

| بخش        | فناوری                    |
|------------|---------------------------|
| Backend    | PHP 8.2 (MVC خالص)        |
| Database   | PostgreSQL 16             |
| Frontend   | رندر سمت سرور + CSS خالص  |
| Container  | Docker Compose            |
| Font       | Sahel (rastikerdar)       |

## شروع کار

### پیش‌نیازها

- Docker & Docker Compose

### نصب

```bash
# کلون کردن پروژه
git clone https://github.com/tayyebi/bindle.git
cd bindle

# ساخت و اجرا
docker compose up -d --build

# اجرای migrations (new workflow)
# These will run via dedicated compose services and are idempotent across runs
docker compose run --rm migrate
docker compose run --rm seed
```

فروشگاه روی `http://localhost:8080` در دسترس خواهد بود.

### تنظیم فروشگاه

1. ادمین سیستم یک فروشگاه جدید در پنل `/admin` می‌سازد
2. فروشنده زیردامنه‌ای مثل `cart.example.com` را با CNAME به سرور بقچه اشاره می‌کند
3. فروشنده لینک افزودن به سبد خرید را در صفحه محصول قرار می‌دهد:
   ```html
   <a href="https://cart.example.com/add?url=https://example.com/product/my-product">افزودن به سبد خرید</a>
   ```
4. فروشنده در داشبورد `/dashboard` تنظیمات و سفارش‌ها را مدیریت می‌کند

### ادمین سیستم

پیش‌فرض: کاربر ادمین سیستم با اجرای migrations/seed.php ایجاد می‌شود.
برای اجرای seed و migrate به صورت جداگانه از سرویس‌های هرکدام استفاده کنید:
```bash
docker compose run --rm migrate
docker compose run --rm seed
```

## مسیرها

### دامنه فروشگاه (از طریق CNAME)
| روش | مسیر            | توضیح                     |
|-----|-----------------|---------------------------|
| GET | `/`             | صفحه اصلی فروشگاه         |
| GET | `/add`          | افزودن محصول به سبد خرید  |
| GET | `/cart`         | مشاهده سبد خرید           |
| POST| `/cart/update`  | بروزرسانی تعداد           |
| POST| `/cart/remove`  | حذف از سبد خرید           |
| GET | `/checkout`     | فرم تسویه حساب            |
| POST| `/checkout`     | ثبت سفارش                 |
| GET | `/order/{token}`| وضعیت سفارش               |
| POST| `/order/{id}/proof`| ارسال رسید پرداخت       |
| GET | `/download/{token}`| دانلود محصول دیجیتال    |

### دامنه اصلی (پنل مدیریت)
| روش | مسیر                              | توضیح              |
|-----|-----------------------------------|--------------------|
| GET | `/login`                          | فرم ورود           |
| POST| `/login`                          | ورود               |
| GET | `/logout`                         | خروج               |
| GET | `/dashboard`                      | داشبورد فروشنده    |
| GET | `/dashboard/orders`               | لیست سفارش‌ها      |
| GET | `/dashboard/orders/{id}`          | جزئیات سفارش       |
| POST| `/dashboard/orders/{id}/approve`  | تأیید پرداخت       |
| POST| `/dashboard/orders/{id}/reject`   | رد پرداخت          |
| GET | `/dashboard/settings`             | تنظیمات فروشگاه    |
| POST| `/dashboard/settings`             | بروزرسانی تنظیمات  |
| GET | `/admin`                          | پنل ادمین سیستم    |
| GET | `/admin/shops`                    | لیست فروشگاه‌ها    |
| GET | `/admin/shops/create`             | فرم ایجاد فروشگاه  |
| POST| `/admin/shops/create`             | ایجاد فروشگاه      |
| POST| `/admin/shops/{id}/toggle`        | فعال/غیرفعال کردن  |
