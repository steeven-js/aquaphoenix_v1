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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('number');
            $table->enum('status', ['en progression', 'livré', 'annulé'])->default('en progression');
            $table->text('notes')->nullable();
            $table->date('published_at')->nullable();
            $table->date('delivered_date')->nullable();
            $table->string('url')->nullable();
            $table->boolean('report_delivered')->default(false);
            $table->date('report_delivered_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
