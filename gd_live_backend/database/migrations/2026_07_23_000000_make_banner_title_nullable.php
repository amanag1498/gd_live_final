<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->string('title', 120)->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('banners')->whereNull('title')->update(['title' => '']);

        Schema::table('banners', function (Blueprint $table) {
            $table->string('title', 120)->nullable(false)->change();
        });
    }
};
