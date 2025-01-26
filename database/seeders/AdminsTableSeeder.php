<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminsTableSeeder extends Seeder
{
    public function run()
    {
        // Очистить таблицу admins
        DB::table('admins')->truncate();

        // Вставить новые данные
        \App\Models\Admin::factory()->create([
            'username' => 'TemhaN',
            'email' => 'temhan@nitokin.hui',
            'password' => bcrypt('05091010'),
        ]);

        \App\Models\Admin::factory()->create([
            'username' => 'MrMeGaBaN',
            'email' => 'mrmegaban@nitokin.hui',
            'password' => bcrypt('111111'),
        ]);
    }
}