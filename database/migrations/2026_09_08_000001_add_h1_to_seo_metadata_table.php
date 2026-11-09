<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seo_metadata', function (Blueprint $table) {
            $table->string('h1')->nullable()->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('seo_metadata', function (Blueprint $table) {
            $table->dropColumn('h1');
        });
    }
};