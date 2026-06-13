<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('host_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('host_requests', 'agency_id')) {
                $table->foreignId('agency_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            }
        });

        if (Schema::hasTable('host_enroll_requests')) {
            Schema::drop('host_enroll_requests');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('host_enroll_requests')) {
            Schema::create('host_enroll_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('host_user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('agency_id')->constrained()->cascadeOnDelete();
                $table->text('message')->nullable();
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('reviewed_at')->nullable();
                $table->text('review_notes')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('host_requests', function (Blueprint $table) {
            if (Schema::hasColumn('host_requests', 'agency_id')) {
                $table->dropConstrainedForeignId('agency_id');
            }
        });
    }
};
