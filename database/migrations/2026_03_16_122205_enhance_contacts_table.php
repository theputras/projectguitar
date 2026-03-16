<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('country')->nullable()->after('phone');
            $table->enum('instrument_type', ['bass', 'guitar', 'other'])->nullable()->after('instrument');
            $table->string('budget_range')->nullable()->after('instrument_type');
            $table->enum('inquiry_type', ['general', 'order', 'technical'])->default('general')->after('message');
            $table->enum('status', ['new', 'read', 'responded'])->default('new')->after('inquiry_type');
            $table->text('admin_notes')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'country', 'instrument_type', 'budget_range',
                'inquiry_type', 'status', 'admin_notes'
            ]);
        });
    }
};
