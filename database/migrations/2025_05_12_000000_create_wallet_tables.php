<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('currency', 3);
            $table->decimal('balance', 15, 2)->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'currency']);
            $table->index(['user_id', 'currency']);
        });

        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('balance_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3);
            $table->string('reference')->nullable();
            $table->string('description')->nullable();
            $table->decimal('fee', 15, 2)->default(0);
            $table->string('type'); // 'credit' or 'debit'
            $table->timestamps();
            $table->index(['user_id', 'currency']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('balances');
    }
};