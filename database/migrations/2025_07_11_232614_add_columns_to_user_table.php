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
        Schema::table('users', function (Blueprint $table) {
            //
            $table->string("username")->unique()->after('id');
            $table->string("image")->nullable()->after('email_verified_at');
            $table->string("bio")->nullable()->after('image');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_username_unique');
            //
            $table->dropColumn('username');
            $table->dropColumn('image');
            $table->dropColumn('bio');
        });
    }
};
