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
        // 1. Add keywords to questions table for AI evaluation
        if (!Schema::hasColumn('questions', 'keywords')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->text('keywords')->nullable()->after('question');
            });
        }

        // 2. Add total_pages to modules table
        if (!Schema::hasColumn('modules', 'total_pages')) {
            Schema::table('modules', function (Blueprint $table) {
                $table->integer('total_pages')->default(1)->after('order_number');
            });
        }

        // 3. Add current_page to user_progress table
        if (!Schema::hasColumn('user_progress', 'current_page')) {
            Schema::table('user_progress', function (Blueprint $table) {
                $table->integer('current_page')->default(1)->after('module_id');
            });
        }

        // 4. Create score_adjustment_logs for recording detailed tutor adjustments
        if (!Schema::hasTable('score_adjustment_logs')) {
            Schema::create('score_adjustment_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_answer_id')->constrained('user_answers')->onDelete('cascade');
                $table->foreignId('tutor_id')->constrained('users')->onDelete('cascade');
                $table->float('original_ai_score')->nullable();
                $table->float('adjusted_score');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 5. Create activity_logs table for audit & activity timeline
        if (!Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
                $table->string('action');
                $table->text('description');
                $table->string('ip_address')->nullable();
                $table->string('user_agent')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('score_adjustment_logs');

        if (Schema::hasColumn('user_progress', 'current_page')) {
            Schema::table('user_progress', function (Blueprint $table) {
                $table->dropColumn('current_page');
            });
        }

        if (Schema::hasColumn('modules', 'total_pages')) {
            Schema::table('modules', function (Blueprint $table) {
                $table->dropColumn('total_pages');
            });
        }

        if (Schema::hasColumn('questions', 'keywords')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->dropColumn('keywords');
            });
        }
    }
};
