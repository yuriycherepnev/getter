<?php

namespace App\Http\Controllers;

class CatalogController extends Controller
{
    public function index(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Ноутбук Dell XPS 13',
                'price' => 999.99,
                'category' => 'Электроника'
            ],
        ];
    }
}
