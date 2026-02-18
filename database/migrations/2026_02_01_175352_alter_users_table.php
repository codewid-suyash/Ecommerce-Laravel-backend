<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        schema::table('users', function (Blueprint $table) {
            $table->string('mobile')->nullable()->after('email');
            $table->text('address')->nullable()->after('mobile');
            $table->string('city')->nullable()->after('address');
            $table->string('state')->nullable()->after('city');
            $table->string('zip')->nullable()->after('state');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['mobile', 'address', 'city', 'state', 'zip']);
        });
    }
};
