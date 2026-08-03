<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SettingsRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        return view('admin.settings.index', [
            'siteName' => setting('site_name', 'To Do'),
            'currencySymbol' => setting('currency_symbol', '$'),
        ]);
    }

    public function update(SettingsRequest $request): RedirectResponse
    {
        Setting::set('site_name', $request->validated('site_name'));
        Setting::set('currency_symbol', $request->validated('currency_symbol'));

        return redirect()
            ->route('admin.settings.index')
            ->with('status', 'Settings saved.');
    }
}
