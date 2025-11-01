<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        return view('pages.app.home');
    }

    // BARU: Tambahkan method ini
    public function transactionDetail()
    {
        return view('pages.app.transactions.detail');
    }
}