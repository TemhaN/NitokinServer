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
        Schema::table('actor_games', function (Blueprint $table) {
            if (Schema::hasColumn('actor_games', 'created_at')) {
                $table->dropColumn('created_at');
            }
            if (Schema::hasColumn('actor_games', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
        });

        Schema::table('favorites', function (Blueprint $table) {
            if (Schema::hasColumn('favorites', 'created_at')) {
                $table->dropColumn('created_at');
            }
            if (Schema::hasColumn('favorites', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
        });

        Schema::table('category_games', function (Blueprint $table) {
            if (Schema::hasColumn('category_games', 'created_at')) {
                $table->dropColumn('created_at');
            }
            if (Schema::hasColumn('category_games', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
        });

        Schema::table('ratings', function (Blueprint $table) {
            if (Schema::hasColumn('ratings', 'created_at')) {
                $table->dropColumn('created_at');
            }
            if (Schema::hasColumn('ratings', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actor_games', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('favorites', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('category_games', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('ratings', function (Blueprint $table) {
            $table->timestamps();
        });
    }
};