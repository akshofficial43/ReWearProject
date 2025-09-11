<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Backfill location for existing official products
        DB::table('products')
            ->where('is_official', true)
            ->whereNull('location')
            ->update(['location' => 'Rewear Official']);
    }

    public function down()
    {
        // Revert only the rows we set (best-effort)
        DB::table('products')
            ->where('is_official', true)
            ->where('location', 'Rewear Official')
            ->update(['location' => null]);
    }
};
