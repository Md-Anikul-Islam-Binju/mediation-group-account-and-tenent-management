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
        Schema::create('owner_flats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id');
            $table->foreign('owner_id')->references('id')->on('owners')->onDelete('cascade');
            $table->string('flat_uniq_code')->unique();
            $table->text('address');
            $table->decimal('monthly_rental_amount', 10, 2);
            $table->decimal('service_charge', 10, 2)->nullable();
            $table->integer('security_deposit_month')->nullable();
            $table->decimal('security_deposit_amount', 10, 2)->nullable();
            $table->text('remark')->nullable();
            $table->enum('status', ['Vacant', 'Booked'])->default('Vacant');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('owner_flats');
    }
};
