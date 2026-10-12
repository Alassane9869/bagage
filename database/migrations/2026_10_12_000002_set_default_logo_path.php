<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('societe')->where('id', 1)->update([
            'logo_path' => 'logos/logo_societe_1782763691.png',
        ]);
    }

    public function down(): void
    {
    }
};
