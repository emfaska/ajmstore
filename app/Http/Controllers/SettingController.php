<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSettingRequest;
use App\Services\SettingService;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    protected SettingService $settingService;

    // Setting keys managed by this module
    private const SETTING_KEYS = [
        'shop_name',
        'shop_address',
        'shop_whatsapp',
        'receipt_footer',
        'default_tax',
        'default_discount',
        'shop_logo',
    ];

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * Display the settings form.
     */
    public function index()
    {
        $settings = [];
        foreach (self::SETTING_KEYS as $key) {
            $settings[$key] = $this->settingService->get($key);
        }

        return view('settings.index', compact('settings'));
    }

    /**
     * Update all store settings.
     */
    public function update(StoreSettingRequest $request)
    {
        $data = $request->validated();

        // Handle logo file upload
        if ($request->hasFile('shop_logo')) {
            // Delete the old logo if it exists
            $oldLogo = $this->settingService->get('shop_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            // Store the new logo
            $logoPath = $request->file('shop_logo')->store('logos', 'public');
            $this->settingService->set('shop_logo', $logoPath);
        }

        // Persist all other settings
        $settableKeys = ['shop_name', 'shop_address', 'shop_whatsapp', 'receipt_footer', 'default_tax', 'default_discount'];
        foreach ($settableKeys as $key) {
            $this->settingService->set($key, $data[$key] ?? null);
        }

        return redirect()->route('settings.index')->with('success', 'Pengaturan toko berhasil disimpan.');
    }
}
