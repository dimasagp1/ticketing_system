<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;

class SettingsHelper
{
    /**
     * Get a system setting value by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        $path = storage_path('app/system-settings.json');
        
        if (!File::exists($path)) {
            // Default fallback
            $defaults = [
                'app_name' => config('app.name', 'Antrian Project'),
                'app_logo' => '',
                'app_favicon' => '',
                'admin_email' => 'admin@antrian.com',
                'per_page' => 15,
                'email_notifications' => true,
                'notification_window_days' => 3,
                'maintenance_mode' => false,
                'company_department_name' => 'DEPARTEMEN INFORMATION TECHNOLOGY',
                'company_subtitle' => 'Formulir Layanan Dukungan Teknis dan Infrastruktur',
                'company_address' => 'Jl. Raya Perusahaan No. 123',
                'company_phone' => '(021) 1234567',
                'company_city' => 'Purbalingga',
                'head_of_it_name' => 'Head of IT / Manager',
                'head_of_it_title' => 'IT Officer / Supervisor',
                'it_supervisor_signature' => '',
            ];
            
            return $defaults[$key] ?? $default;
        }

        $settings = json_decode(File::get($path), true);
        
        if (is_array($settings) && array_key_exists($key, $settings)) {
            return $settings[$key];
        }

        return $default;
    }
}
