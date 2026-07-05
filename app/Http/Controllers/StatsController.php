<?php

namespace App\Http\Controllers;

use App\Services\StatsService;
use Illuminate\View\View;

class StatsController extends Controller
{
    public function index(): View
    {
        $stats = (new StatsService)->compute(auth()->id());

        return view('stats.index', $stats);
    }
}
