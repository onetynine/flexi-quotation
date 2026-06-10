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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_no')->unique();
            $table->string('customer_name');
            $table->string('email')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('delivery_address')->nullable();

            $table->foreignId('plan_id')->nullable()->constrained('plans')->nullOnDelete();
            $table->string('plan_name');
            $table->string('plan_specs')->nullable();
            $table->boolean('is_custom_plan')->default(false);
            $table->integer('quantity')->default(1);

            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_days');

            $table->string('delivery_option')->default('Self Collect');
            $table->decimal('delivery_fee', 10, 2)->default(0);

            $table->decimal('rate_per_day', 10, 2);
            $table->decimal('rental_fee', 10, 2);
            $table->string('deposit_option')->default('standard');
            $table->decimal('deposit_amount', 10, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(6);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('total_payable', 10, 2)->default(0);

            $table->string('agent_name')->nullable();
            $table->string('agent_contact')->nullable();
            $table->string('agent_email')->nullable();

            $table->string('quotation_link')->nullable();
            $table->string('status')->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
