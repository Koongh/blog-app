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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscriber_id')->constrained('users')->onDelete('cascade'); // siapa yang follow
            $table->foreignId('subscribed_to_id')->constrained('users')->onDelete('cascade'); // siapa yang di-follow
            $table->timestamps();

            $table->unique(['subscriber_id', 'subscribed_to_id']); // biar tidak dobel follow
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
