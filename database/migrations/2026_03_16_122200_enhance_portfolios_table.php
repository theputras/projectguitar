<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('portfolios', function (Blueprint $table) {
            $table->string('slug')->unique()->after('title');
            $table->string('wood_type')->nullable()->after('category');
            $table->string('pickup')->nullable()->after('wood_type');
            $table->string('scale_length')->nullable()->after('pickup');
            $table->string('finish')->nullable()->after('scale_length');
            $table->integer('strings')->nullable()->after('finish');
            $table->string('price_range')->nullable()->after('strings');
            $table->json('gallery')->nullable()->after('image');
            $table->json('specifications')->nullable()->after('gallery');
            $table->boolean('is_featured')->default(false)->after('specifications');
            $table->enum('status', ['draft', 'published'])->default('published')->after('is_featured');
        });
    }

    public function down()
    {
        Schema::table('portfolios', function (Blueprint $table) {
            $table->dropColumn([
                'slug', 'wood_type', 'pickup', 'scale_length', 'finish',
                'strings', 'price_range', 'gallery', 'specifications',
                'is_featured', 'status'
            ]);
        });
    }
};
