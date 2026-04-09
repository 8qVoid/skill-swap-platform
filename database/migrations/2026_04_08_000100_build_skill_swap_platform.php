<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('teach-and-learn')->after('password');
            $table->string('profile_photo')->nullable()->after('remember_token');
            $table->text('bio')->nullable()->after('profile_photo');
            $table->string('location')->nullable()->after('bio');
            $table->string('timezone')->nullable()->after('location');
            $table->string('availability')->nullable()->after('timezone');
            $table->string('skill_level')->nullable()->after('availability');
            $table->json('teach_skills')->nullable()->after('skill_level');
            $table->json('learn_skills')->nullable()->after('teach_skills');
            $table->json('formats')->nullable()->after('learn_skills');
            $table->json('portfolio_links')->nullable()->after('formats');
            $table->json('saved_users')->nullable()->after('portfolio_links');
            $table->boolean('onboarding_completed')->default(false)->after('saved_users');
            $table->boolean('is_verified')->default(false)->after('onboarding_completed');
        });

        Schema::create('swap_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
            $table->string('skill_to_learn');
            $table->string('skill_to_offer');
            $table->text('message');
            $table->string('proposed_schedule');
            $table->string('preferred_format');
            $table->string('status')->default('pending');
            $table->text('counter_message')->nullable();
            $table->string('counter_schedule')->nullable();
            $table->string('counter_format')->nullable();
            $table->timestamps();
        });

        Schema::create('swaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('swap_request_id')->unique()->constrained('swap_requests')->cascadeOnDelete();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
            $table->string('skill_to_learn');
            $table->string('skill_to_offer');
            $table->string('format');
            $table->string('status')->default('active');
            $table->text('progress_notes')->nullable();
            $table->unsignedTinyInteger('progress_percent')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('swap_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('swap_id')->constrained('swaps')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->timestamps();
        });

        Schema::create('swap_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('swap_id')->constrained('swaps')->cascadeOnDelete();
            $table->timestamp('scheduled_for');
            $table->string('meeting_link')->nullable();
            $table->string('topic');
            $table->string('status')->default('upcoming');
            $table->timestamps();
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('swap_id')->constrained('swaps')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reviewee_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('feedback');
            $table->timestamps();

            $table->unique(['swap_id', 'reviewer_id']);
        });

        Schema::create('user_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reported_user_id')->constrained('users')->cascadeOnDelete();
            $table->text('reason');
            $table->timestamps();
        });

        Schema::create('blocked_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blocker_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('blocked_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['blocker_id', 'blocked_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blocked_users');
        Schema::dropIfExists('user_reports');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('swap_sessions');
        Schema::dropIfExists('swap_messages');
        Schema::dropIfExists('swaps');
        Schema::dropIfExists('swap_requests');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'profile_photo',
                'bio',
                'location',
                'timezone',
                'availability',
                'skill_level',
                'teach_skills',
                'learn_skills',
                'formats',
                'portfolio_links',
                'saved_users',
                'onboarding_completed',
                'is_verified',
            ]);
        });
    }
};
