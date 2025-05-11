<?php

namespace App\Http\Controllers\AdminKemahasiswaan;

use App\Http\Controllers\AdminKemahasiswaanController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SuratController extends AdminKemahasiswaanController
{
    public $breadcrumbItems = [
        ['label' => 'Surat'],
    ];

    public $title = 'Surat';
    public function index()
    {
        return view('dashboard.admin-kemahasiswaan.surat.index', [
            'menuItems' => $this->menuItems,
            'breadcrumbItems' => $this->breadcrumbItems,
            'title' => $this->title
        ]);
    }
}
