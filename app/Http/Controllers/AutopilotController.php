<?php

namespace App\Http\Controllers;

use App\Models\AutopilotSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutopilotController extends Controller
{
    public function index()
    {
        $setting = AutopilotSetting::query()->firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'enabled' => false,
                'mode' => 'manual',
                'posts_per_day' => 2,
                'timezone' => 'Asia/Jakarta',
                'language' => 'id',
                'tone' => 'santai',
                'image_enabled' => true,
                'auto_publish' => false,
                'require_approval' => true,
                'minimum_quality_score' => 75,
                'minimum_inventory' => 5,
                'target_inventory' => 14,
                'categories' => ['Tips Mancing', 'Nila', 'Mujair', 'Fishing Lifestyle'],
            ]
        );

        return view('autopilot.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'enabled' => ['nullable', 'boolean'],
            'mode' => ['required', 'in:manual,semi-auto,full-autopilot'],
            'posts_per_day' => ['nullable', 'integer', 'min:1', 'max:20'],
            'timezone' => ['nullable', 'string', 'max:80'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'image_enabled' => ['nullable', 'boolean'],
            'auto_publish' => ['nullable', 'boolean'],
            'require_approval' => ['nullable', 'boolean'],
            'minimum_inventory' => ['nullable', 'integer', 'min:1', 'max:100'],
            'target_inventory' => ['nullable', 'integer', 'min:1', 'max:100'],
            'minimum_quality_score' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $setting = AutopilotSetting::query()->firstOrCreate(['user_id' => Auth::id()]);
        $setting->fill($validated);
        $setting->save();

        return redirect()->route('autopilot.index')->with('success', 'Pengaturan autopilot berhasil diperbarui.');
    }
}
