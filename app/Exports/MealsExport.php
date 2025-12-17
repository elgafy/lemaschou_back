<?php

namespace App\Exports;

use App\Models\Meal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class MealsExport implements FromView
{
    public function view(): View
    {
        return view('exports.meals', [
            'meals' => Meal::all()
        ]);
    }
}
