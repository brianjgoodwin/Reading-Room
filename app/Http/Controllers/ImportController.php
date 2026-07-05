<?php

namespace App\Http\Controllers;

use App\Services\GoodreadsImporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ImportController extends Controller
{
    public function create(): View
    {
        return view('import.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
            'import_reviews' => ['boolean'],
        ]);

        $path = $request->file('csv_file')->store('imports', 'local');
        $fullPath = Storage::disk('local')->path($path);

        $results = (new GoodreadsImporter)->import(
            csvPath: $fullPath,
            userId: auth()->id(),
            importReviews: (bool) $request->input('import_reviews', false),
        );

        // Clean up the temp file
        Storage::disk('local')->delete($path);

        $message = "Import complete: {$results['imported']} books imported, {$results['skipped']} skipped.";

        return redirect()->route('import.create')
            ->with('import_results', $results)
            ->with('success', $message);
    }
}
