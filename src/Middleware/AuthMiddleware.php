<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\View;

class AuthMiddleware
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle(): bool
    {
        if (!$this->request->hasSession('shop_id') && !$this->request->hasSession('admin_id')) {
            View::redirect('/login');
            return false;
        }
        return true;
    }
}
