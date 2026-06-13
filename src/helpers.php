<?php

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function asset(string $path): string
{
    return $path;
}

function trans(string $key): string
{
    $messages = [
        'app.name' => 'بقچه',
        'cart' => 'سبد خرید',
        'checkout' => 'تسویه حساب',
        'orders' => 'سفارش‌ها',
        'login' => 'ورود',
        'logout' => 'خروج',
        'dashboard' => 'داشبورد',
        'settings' => 'تنظیمات',
        'submit' => 'ثبت',
        'cancel' => 'لغو',
        'approve' => 'تأیید',
        'reject' => 'رد',
        'pending' => 'در انتظار',
        'approved' => 'تأیید شده',
        'rejected' => 'رد شده',
        'cancelled' => 'لغو شده',
        'add_to_cart' => 'افزودن به سبد خرید',
        'view_cart' => 'مشاهده سبد خرید',
        'empty_cart' => 'سبد خرید خالی است',
        'total' => 'مجموع',
        'price' => 'قیمت',
        'quantity' => 'تعداد',
        'remove' => 'حذف',
        'continue_shopping' => 'ادامه خرید',
        'checkout_now' => 'تسویه حساب',
        'customer_info' => 'اطلاعات مشتری',
        'full_name' => 'نام و نام خانوادگی',
        'email' => 'ایمیل',
        'shipping_address' => 'آدرس تحویل',
        'order_summary' => 'خلاصه سفارش',
        'order_placed' => 'سفارش شما ثبت شد',
        'order_number' => 'شماره سفارش',
        'payment_instructions' => 'دستورالعمل پرداخت',
        'upload_proof' => 'آپلود رسید پرداخت',
        'proof_screenshot' => 'تصویر رسید',
        'proof_transaction_id' => 'شماره تراکنش',
        'order_status' => 'وضعیت سفارش',
        'download' => 'دانلود',
        'my_orders' => 'سفارش‌های من',
        'shop_settings' => 'تنظیمات فروشگاه',
        'domain' => 'دامنه',
        'shop_name' => 'نام فروشگاه',
        'payment_instructions_label' => 'دستورالعمل پرداخت',
        'webhook_url' => 'آدرس وب‌هوک',
        'save' => 'ذخیره',
        'actions' => 'عملیات',
        'customer' => 'مشتری',
        'date' => 'تاریخ',
        'status' => 'وضعیت',
        'admins' => 'مدیران سیستم',
        'shops' => 'فروشگاه‌ها',
        'create_shop' => 'ایجاد فروشگاه',
        'is_active' => 'فعال',
        'yes' => 'بله',
        'no' => 'خیر',
        'back' => 'بازگشت',
        'error_404' => 'صفحه مورد نظر یافت نشد',
        'error_500' => 'خطای داخلی سرور',
        'product' => 'محصول',
        'products' => 'محصولات',
        'shop' => 'فروشگاه',
        'home' => 'خانه',
        'no_orders' => 'هیچ سفارشی یافت نشد',
        'all_rights' => 'تمامی حقوق محفوظ است',
        'persian_numbers' => true,
    ];

    return $messages[$key] ?? $key;
}

function pnum(string|int|float $value): string
{
    $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    return str_replace($english, $persian, (string) $value);
}

function c(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
