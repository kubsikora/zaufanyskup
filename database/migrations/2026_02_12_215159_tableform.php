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
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->string(column: 'name')->nullable(); 
            $table->string('email')->nullable();      
            $table->string(column: 'tel')->nullable(); 
            $table->string(column: 'other')->nullable(); 
            $table->longText(column: 'message')->nullable(); 
            $table->string(column: 'ip')->nullable(); 
            $table->timestamps();       
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forms');
    }
};
