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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->unsignedBigInteger('rent_id');

            $table->unsignedBigInteger('owner_id');
            $table->string('owner_name');

            $table->unsignedBigInteger('flat_id');
            $table->string('flat_address');

            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('tenant_name')->nullable();

            $table->decimal('monthly_rental_amount', 10, 2)->default(0);
            $table->decimal('service_charge', 10, 2)->default(0);

            $table->date('date'); // bill generate date
            $table->string('month')->nullable();

            $table->json('is_extra_amount')->nullable();
            // example: {"gas_bill":700,"bet_bill":1500}

            $table->decimal('total_amount', 10, 2)->nullable();
            $table->decimal('due_amount', 10, 2)->nullable();
            $table->decimal('paid_amount', 10, 2)->nullable();

            $table->text('remark')->nullable();

            $table->string('status')->default('pending');
            // pending, paid, partial

            $table->timestamps();

            // Optional Foreign Keys (uncomment if tables exist)
            $table->foreign('owner_id')->references('id')->on('owners')->cascadeOnDelete();
            $table->foreign('flat_id')->references('id')->on('owner_flats')->cascadeOnDelete();
            $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
