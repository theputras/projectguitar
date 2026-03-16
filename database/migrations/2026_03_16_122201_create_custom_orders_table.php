<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('custom_orders', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_country');
            $table->string('customer_phone')->nullable();
            $table->enum('order_type', ['custom_bass', 'custom_guitar', 'other'])->default('custom_bass');
            $table->json('requirements')->nullable();
            $table->string('budget')->nullable();
            $table->string('timeline')->nullable();
            $table->enum('current_step', ['consultation', 'design', 'build', 'quality_check', 'shipping', 'completed'])->default('consultation');
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->string('tracking_token')->unique()->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('custom_orders');
    }
};
