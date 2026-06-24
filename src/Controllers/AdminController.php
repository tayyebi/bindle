<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\View;
use App\Models\Shop;
use App\Models\SystemAdmin;

class AdminController
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function loginForm(): string
    {
        if ($this->request->hasSession('shop_id') || $this->request->hasSession('admin_id')) {
            if ($this->request->hasSession('admin_id')) {
                View::redirect('/admin');
            } else {
                View::redirect('/dashboard');
            }
            return '';
        }
        return View::render('shop/login', [], true, 'admin');
    }

    public function login(): string
    {
        $email = trim($this->request->input('email', ''));
        $password = $this->request->input('password', '');
        $isAdmin = $this->request->input('is_admin', '') === '1';

        if ($isAdmin) {
            $admin = SystemAdmin::verifyPassword($email, $password);
            if ($admin) {
                if (!empty($admin['totp_enabled'])) {
                    $_SESSION['pending_2fa_type'] = 'admin';
                    $_SESSION['pending_2fa_id'] = $admin['id'];
                    View::redirect('/totp/verify');
                    return '';
                }
                $this->request->setSession('admin_id', $admin['id']);
                View::redirect('/admin');
                return '';
            }
        } else {
            $shop = Shop::verifyPassword($email, $password);
            if ($shop) {
                if (!$shop['is_active']) {
                    return View::render('shop/login', [
                        'error' => 'فروشگاه غیرفعال است',
                    ], true, 'admin');
                }
                if (!empty($shop['totp_enabled'])) {
                    $_SESSION['pending_2fa_type'] = 'shop';
                    $_SESSION['pending_2fa_id'] = $shop['id'];
                    View::redirect('/totp/verify');
                    return '';
                }
                $this->request->setSession('shop_id', $shop['id']);
                View::redirect('/dashboard');
                return '';
            }
        }

        return View::render('shop/login', [
            'error' => 'اطلاعات وارد شده صحیح نیست',
        ], true, 'admin');
    }

    public function logout(): string
    {
        $this->request->removeSession('shop_id');
        $this->request->removeSession('admin_id');
        session_destroy();
        View::redirect('/login');
        return '';
    }

    public function index(): string
    {
        if (!$this->request->hasSession('admin_id')) {
            View::redirect('/admin/login');
            return '';
        }

        $shops = Shop::all();
        return View::render('admin/index', [
            'shops' => $shops,
            'totalShops' => count($shops),
        ], true, 'admin');
    }

    public function shops(): string
    {
        if (!$this->request->hasSession('admin_id')) {
            View::redirect('/admin/login');
            return '';
        }

        $allShops = Shop::all();
        return View::render('admin/shops', [
            'shops' => $allShops,
        ], true, 'admin');
    }

    public function createShopForm(): string
    {
        if (!$this->request->hasSession('admin_id')) {
            View::redirect('/admin/login');
            return '';
        }

        return View::render('admin/create-shop', [], true, 'admin');
    }

    public function createShop(): string
    {
        if (!$this->request->hasSession('admin_id')) {
            View::redirect('/admin/login');
            return '';
        }

        $domain = trim($this->request->input('domain', ''));
        $name = trim($this->request->input('name', ''));
        $email = trim($this->request->input('email', ''));
        $password = $this->request->input('password', '');

        $errors = [];
        if (empty($domain)) $errors[] = 'دامنه الزامی است';
        if (empty($name)) $errors[] = 'نام فروشگاه الزامی است';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'ایمیل معتبر الزامی است';
        if (strlen($password) < 6) $errors[] = 'رمز عبور باید حداقل ۶ کاراکتر باشد';

        if (!empty($errors)) {
            return View::render('admin/create-shop', [
                'errors' => $errors,
                'old' => $_POST,
            ], true, 'admin');
        }

        Shop::create([
            'domain' => $domain,
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        View::redirect('/admin/shops');
        return '';
    }

    public function toggleShop(string $id): string
    {
        if (!$this->request->hasSession('admin_id')) {
            View::redirect('/admin/login');
            return '';
        }

        Shop::toggleActive($id);
        View::redirect('/admin/shops');
        return '';
    }
}
