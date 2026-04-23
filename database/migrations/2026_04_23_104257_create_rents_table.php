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
        Schema::create('rents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id');
            $table->unsignedBigInteger('flat_id');
            $table->unsignedBigInteger('tenant_id');
            $table->decimal('monthly_rental_amount', 10, 2);
            $table->decimal('service_charge', 10, 2)->nullable();
            $table->date('date'); // rent month or payment date
            $table->text('remark')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            // Foreign key constraints (optional but recommended)
            $table->foreign('owner_id')->references('id')->on('owners')->onDelete('cascade');
            $table->foreign('flat_id')->references('id')->on('owner_flats')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rents');
    }
};
