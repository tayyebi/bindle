<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\View;

class HomeController
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index(): string
    {
        $shop = $this->request->getShopContext();
        return View::render('home/shop', [
            'shop' => $shop,
        ]);
    }

    public function landing(): string
    {
        return View::render('home/landing', [], false);
    }
}
