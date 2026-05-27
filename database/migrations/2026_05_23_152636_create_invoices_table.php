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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('customer_id')->constrained();
            $table->date('date');
            $table->date('due_date')->nullable();
            $table->string('status')->default('Draft'); // Draft, Dikirim, Dibayar Sebagian, Lunas, Jatuh Tempo, Dibatalkan
            $table->decimal('dp_amount', 15, 2)->default(0);
            $table->decimal('dp_percent', 5, 2)->default(0);
            $table->boolean('use_ppn')->default(false); // PPN 11% toggle
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
