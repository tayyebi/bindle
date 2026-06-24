<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\View;
use App\Services\SystemWebhookService;

class SystemWebhookController
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    private function requireAdmin(): void
    {
        if (!$this->request->hasSession('admin_id')) {
            View::redirect('/admin/login');
            exit;
        }
    }

    public function index(): string
    {
        $this->requireAdmin();
        $webhooks = SystemWebhookService::getAll();

        return View::render('system/webhooks', [
            'title' => 'مدیریت وب‌هوک‌های سیستم',
            'webhooks' => $webhooks,
        ], true, 'admin');
    }

    public function createForm(): string
    {
        $this->requireAdmin();

        return View::render('system/webhook-form', [
            'title' => 'ایجاد وب‌هوک جدید',
            'webhook' => null,
        ], true, 'admin');
    }

    public function create(): string
    {
        $this->requireAdmin();

        $url = trim($this->request->input('url', ''));
        $events = $this->request->input('events', []);
        $description = trim($this->request->input('description', ''));

        $errors = [];
        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            $errors[] = 'آدرس وب‌هوک معتبر نیست';
        }
        if (empty($events) || !is_array($events)) {
            $errors[] = 'حداقل یک رویداد را انتخاب کنید';
        }

        if (!empty($errors)) {
            return View::render('system/webhook-form', [
                'title' => 'ایجاد وب‌هوک جدید',
                'webhook' => null,
                'errors' => $errors,
                'old' => $_POST,
            ], true, 'admin');
        }

        SystemWebhookService::create([
            'url' => $url,
            'events' => implode(',', $events),
            'is_active' => $this->request->input('is_active', '1') === '1',
            'description' => $description,
        ]);

        View::redirect('/system/webhooks');
        return '';
    }

    public function editForm(string $id): string
    {
        $this->requireAdmin();
        $webhook = SystemWebhookService::findById($id);

        if (!$webhook) {
            View::redirect('/system/webhooks');
            return '';
        }

        return View::render('system/webhook-form', [
            'title' => 'ویرایش وب‌هوک',
            'webhook' => $webhook,
        ], true, 'admin');
    }

    public function edit(string $id): string
    {
        $this->requireAdmin();
        $webhook = SystemWebhookService::findById($id);

        if (!$webhook) {
            View::redirect('/system/webhooks');
            return '';
        }

        $url = trim($this->request->input('url', ''));
        $events = $this->request->input('events', []);
        $description = trim($this->request->input('description', ''));

        $errors = [];
        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            $errors[] = 'آدرس وب‌هوک معتبر نیست';
        }
        if (empty($events) || !is_array($events)) {
            $errors[] = 'حداقل یک رویداد را انتخاب کنید';
        }

        if (!empty($errors)) {
            return View::render('system/webhook-form', [
                'title' => 'ویرایش وب‌هوک',
                'webhook' => $webhook,
                'errors' => $errors,
                'old' => $_POST,
            ], true, 'admin');
        }

        SystemWebhookService::update($id, [
            'url' => $url,
            'events' => implode(',', $events),
            'is_active' => $this->request->input('is_active', '1') === '1',
            'description' => $description,
        ]);

        View::redirect('/system/webhooks');
        return '';
    }

    public function toggle(string $id): string
    {
        $this->requireAdmin();
        $webhook = SystemWebhookService::findById($id);

        if ($webhook) {
            SystemWebhookService::update($id, [
                'is_active' => !$webhook['is_active'],
            ]);
        }

        View::redirect('/system/webhooks');
        return '';
    }

    public function delete(string $id): string
    {
        $this->requireAdmin();
        SystemWebhookService::delete($id);
        View::redirect('/system/webhooks');
        return '';
    }

    public function test(string $id): string
    {
        $this->requireAdmin();
        $webhook = SystemWebhookService::findById($id);

        if ($webhook) {
            SystemWebhookService::dispatch('test', [
                'event' => 'test',
                'message' => 'این یک پیام تست از بقچه است',
                'time' => date('c'),
            ]);
        }

        View::redirect('/system/webhooks');
        return '';
    }
}
