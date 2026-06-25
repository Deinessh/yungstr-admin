<?php

namespace App\Http\Controllers;

use App\Services\VisitPageService;
use Illuminate\View\View;

class VisitController extends Controller
{
    public function __invoke(VisitPageService $visitPage): View
    {
        $data = $visitPage->pageData();

        return view('visit', [
            'title' => $data['title'],
            'subtitle' => $data['subtitle'],
            'logoUrl' => $data['logo_url'],
            'links' => $data['links'],
        ]);
    }
}
