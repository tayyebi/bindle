<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\View;
use App\Models\Shop;
use App\Models\SystemAdmin;
use App\Services\TotpService;

class ProfileController
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    private function getUser(): ?array
    {
        $adminId = $this->request->session('admin_id');
        if ($adminId) {
            $admin = SystemAdmin::findById($adminId);
            if ($admin) {
                $admin['_type'] = 'admin';
                return $admin;
            }
        }
        $shopId = $this->request->session('shop_id');
        if ($shopId) {
            $shop = Shop::findById($shopId);
            if ($shop) {
                $shop['_type'] = 'shop';
                return $shop;
            }
        }
        return null;
    }

    private function requireAuth(): ?array
    {
        $user = $this->getUser();
        if (!$user) {
            View::redirect('/login');
            return null;
        }
        return $user;
    }

    public function index(): string
    {
        $user = $this->requireAuth();
        if (!$user) return '';

        return View::render('profile/index', [
            'user' => $user,
            'title' => 'پروفایل',
        ], true, 'admin');
    }

    public function updatePassword(): string
    {
        $user = $this->requireAuth();
        if (!$user) return '';

        $current = $this->request->input('current_password', '');
        $new = $this->request->input('new_password', '');
        $confirm = $this->request->input('confirm_password', '');

        $errors = [];

        if ($user['_type'] === 'admin') {
            $admin = SystemAdmin::findById($user['id']);
            if (!$admin || !password_verify($current, $admin['password_hash'])) {
                $errors[] = 'رمز عبور فعلی اشتباه است';
            }
        } else {
            $shop = Shop::findById($user['id']);
            if (!$shop || !password_verify($current, $shop['password_hash'])) {
                $errors[] = 'رمز عبور فعلی اشتباه است';
            }
        }

        if (strlen($new) < 6) {
            $errors[] = 'رمز عبور جدید باید حداقل ۶ کاراکتر باشد';
        }

        if ($new !== $confirm) {
            $errors[] = 'رمز عبور جدید و تکرار آن یکسان نیست';
        }

        if (!empty($errors)) {
            return View::render('profile/index', [
                'user' => $user,
                'title' => 'پروفایل',
                'password_errors' => $errors,
            ], true, 'admin');
        }

        $hash = password_hash($new, PASSWORD_BCRYPT);
        if ($user['_type'] === 'admin') {
            SystemAdmin::updatePassword($user['id'], $hash);
        } else {
            Shop::updatePassword($user['id'], $hash);
        }

        return View::render('profile/index', [
            'user' => $user,
            'title' => 'پروفایل',
            'password_success' => 'رمز عبور با موفقیت تغییر کرد',
        ], true, 'admin');
    }

    public function totpSetup(): string
    {
        $user = $this->requireAuth();
        if (!$user) return '';

        $secret = TotpService::generateSecret();
        $label = $user['_type'] === 'admin' ? $user['username'] : $user['email'];
        $uri = TotpService::getProvisioningUri($secret, $label);
        $qrUrl = TotpService::getQrCodeUrl($uri);

        return View::render('profile/totp-setup', [
            'user' => $user,
            'title' => 'تنظیمات تأیید دو مرحله‌ای',
            'secret' => $secret,
            'provisioning_uri' => $uri,
            'qr_url' => $qrUrl,
        ], true, 'admin');
    }

    public function totpEnable(): string
    {
        $user = $this->requireAuth();
        if (!$user) return '';

        $secret = $this->request->input('secret', '');
        $code = $this->request->input('code', '');

        if (empty($secret) || empty($code)) {
            View::redirect('/profile/totp');
            return '';
        }

        if (!TotpService::verify($secret, $code)) {
            return View::render('profile/totp-setup', [
                'user' => $user,
                'title' => 'تنظیمات تأیید دو مرحله‌ای',
                'secret' => $secret,
                'provisioning_uri' => TotpService::getProvisioningUri(
                    $secret,
                    $user['_type'] === 'admin' ? $user['username'] : $user['email']
                ),
                'qr_url' => TotpService::getQrCodeUrl(TotpService::getProvisioningUri(
                    $secret,
                    $user['_type'] === 'admin' ? $user['username'] : $user['email']
                )),
                'error' => 'کد تأیید اشتباه است. لطفاً دوباره تلاش کنید.',
            ], true, 'admin');
        }

        if ($user['_type'] === 'admin') {
            SystemAdmin::enableTotp($user['id'], $secret);
        } else {
            Shop::enableTotp($user['id'], $secret);
        }

        return View::render('profile/index', [
            'user' => $user,
            'title' => 'پروفایل',
            'totp_success' => 'تأیید دو مرحله‌ای با موفقیت فعال شد',
        ], true, 'admin');
    }

    public function totpDisable(): string
    {
        $user = $this->requireAuth();
        if (!$user) return '';

        if ($user['_type'] === 'admin') {
            SystemAdmin::disableTotp($user['id']);
        } else {
            Shop::disableTotp($user['id']);
        }

        return View::render('profile/index', [
            'user' => $user,
            'title' => 'پروفایل',
            'totp_success' => 'تأیید دو مرحله‌ای غیرفعال شد',
        ], true, 'admin');
    }

    public function totpVerifyForm(): string
    {
        if (empty($_SESSION['pending_2fa_type']) || empty($_SESSION['pending_2fa_id'])) {
            View::redirect('/login');
            return '';
        }

        return View::render('profile/totp-verify', [
            'title' => 'تأیید دو مرحله‌ای',
            'error' => $this->request->query('error', ''),
        ], true, 'admin');
    }

    public function totpVerify(): string
    {
        if (empty($_SESSION['pending_2fa_type']) || empty($_SESSION['pending_2fa_id'])) {
            View::redirect('/login');
            return '';
        }

        $type = $_SESSION['pending_2fa_type'];
        $id = $_SESSION['pending_2fa_id'];
        $code = $this->request->input('code', '');

        $secret = '';
        if ($type === 'admin') {
            $admin = SystemAdmin::findById($id);
            if ($admin) $secret = $admin['totp_secret'] ?? '';
        } else {
            $shop = Shop::findById($id);
            if ($shop) $secret = $shop['totp_secret'] ?? '';
        }

        if (empty($secret) || !TotpService::verify($secret, $code)) {
            unset($_SESSION['pending_2fa_type'], $_SESSION['pending_2fa_id']);
            return View::render('profile/totp-verify', [
                'title' => 'تأیید دو مرحله‌ای',
                'error' => 'کد تأیید اشتباه است',
            ], true, 'admin');
        }

        if ($type === 'admin') {
            $this->request->setSession('admin_id', $id);
        } else {
            $this->request->setSession('shop_id', $id);
        }
        unset($_SESSION['pending_2fa_type'], $_SESSION['pending_2fa_id']);

        View::redirect($type === 'admin' ? '/admin' : '/dashboard');
        return '';
    }
}
