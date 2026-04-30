<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $settings = SystemSetting::pluck('value', 'key');

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'panel' => ['required', 'in:system,accessibility'],
        ]);

        if ($request->panel === 'system') {
            $employee = Auth::guard('employee')->user();

            if (! $employee || $employee->role !== 'owner') {
                abort(403, 'Only the owner can change system settings.');
            }

            $request->validate([
                'timezone' => ['required', 'string', 'max:100'],
                'language' => ['required', 'string', 'max:50'],
            ]);

            SystemSetting::updateOrCreate(['key' => 'timezone'],             ['value' => $request->timezone]);
            SystemSetting::updateOrCreate(['key' => 'language'],             ['value' => $request->language]);
            SystemSetting::updateOrCreate(['key' => 'alert_notification'],   ['value' => $request->boolean('alert_notification')   ? '1' : '0']);
            SystemSetting::updateOrCreate(['key' => 'confirmation_messages'],['value' => $request->boolean('confirmation_messages') ? '1' : '0']);
            SystemSetting::updateOrCreate(['key' => 'auto_backup'],          ['value' => $request->boolean('auto_backup')          ? '1' : '0']);
        }

        if ($request->panel === 'accessibility') {
            SystemSetting::updateOrCreate(['key' => 'dark_mode'],       ['value' => $request->boolean('dark_mode')  ? '1' : '0']);
            SystemSetting::updateOrCreate(['key' => 'color_correction'],['value' => $request->color_correction ?? 'none']);
            SystemSetting::updateOrCreate(['key' => 'system_sound'],    ['value' => $request->boolean('system_sound') ? '1' : '0']);
            Cache::forget('setting.dark_mode');
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings saved.');
    }
}