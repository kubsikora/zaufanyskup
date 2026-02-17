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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable(); 
            $table->string('name')->nullable();      
            
            $table->decimal('price', 8, 2)->default(0)->nullable(); 
            
            $table->string('dimensions')->nullable();   
            $table->string('material')->nullable(); 
            $table->string('prodCode')->unique()->nullable(); 
            $table->text('description')->nullable();   
            $table->boolean('active')->default(1);
            $table->integer(column: 'addeb_by')->nullable(); 
            $table->timestamps();       
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};