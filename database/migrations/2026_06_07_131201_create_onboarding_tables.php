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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutor_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('level')->default('beginner'); // beginner, intermediate, advanced
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->string('title');
            $table->longText('content')->nullable();
            $table->string('video_url')->nullable();
            $table->integer('order_number')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->string('title');
            $table->string('type')->default('multiple_choice'); // multiple_choice, essay
            $table->integer('passing_grade')->default(60);
            $table->integer('duration')->default(30); // in minutes
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->onDelete('cascade');
            $table->text('question');
            $table->string('type')->default('multiple_choice'); // multiple_choice, essay
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('user_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('module_id')->constrained('modules')->onDelete('cascade');
            $table->string('status')->default('completed'); // completed
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('user_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->text('answer')->nullable();
            $table->float('ai_score')->nullable();
            $table->text('ai_feedback')->nullable();
            $table->float('final_score')->nullable();
            $table->timestamps();
        });

        Schema::create('essay_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_answer_id')->constrained('user_answers')->onDelete('cascade');
            $table->float('ai_score')->nullable();
            $table->text('ai_feedback')->nullable();
            $table->float('tutor_score')->nullable();
            $table->text('tutor_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('essay_reviews');
        Schema::dropIfExists('user_answers');
        Schema::dropIfExists('user_progress');
        Schema::dropIfExists('options');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('quizzes');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('courses');
    }
};
