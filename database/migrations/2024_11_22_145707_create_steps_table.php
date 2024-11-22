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
        Schema::create('steps', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('default_commission', 10, 2)->nullable();
            $table->unsignedInteger('average_execution_time');
            $table->timestamps();
            $table->softDeletes(); // Opcional: permite arquivar etapas antigas
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('steps');
    }
};
