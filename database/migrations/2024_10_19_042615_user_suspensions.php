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
        Schema::create('user_suspensions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('banned_by')->constrained('users')->onDelete('cascade');
            $table->enum('ban_type', ['permanent', 'temporary', 'unbanned']);
            $table->string('ban_reason')->nullable();
            $table->timestamp('ban_start');
            $table->timestamp('ban_end')->nullable(); // Optional for temporary bans
            $table->timestamps();
            $table->softDeletes();  
            $table->foreign('user_id', 'unique_user_suspension_fk')
            ->references('id')
            ->on('users')
            ->onDelete('cascade');
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_suspensions');
    }
};
