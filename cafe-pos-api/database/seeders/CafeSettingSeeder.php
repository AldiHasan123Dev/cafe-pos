<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class CafeSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'cafe_name' => 'My Cafe',
            'cafe_address' => '',
            'cafe_phone' => '',
            'currency' => 'IDR',
            'timezone' => 'Asia/Jakarta',
            'tax_percentage' => '0',
            'service_charge_percentage' => '0',
            'receipt_footer' => 'Terima kasih telah berkunjung!',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'type' => 'string',
                ]
            );
        }
    }
}