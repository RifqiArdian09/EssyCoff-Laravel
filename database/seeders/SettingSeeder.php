<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::create([
            'store_name' => 'KasirKu',
            'store_address' => 'Jl. Contoh No.1',
            'store_phone' => '08123456789',
            'payment_methods' => 'cash',
        ]);
    }
}
