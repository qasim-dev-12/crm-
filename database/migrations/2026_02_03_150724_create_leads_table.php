<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
          $table->id();
    $table->uuid('slug')->unique();

    $table->string('salutation')->nullable(); // Mr, Ms, etc
    $table->string('name');
    $table->foreignId('service_type_id')->constrained('service_types');
    $table->string('area')->nullable();
    $table->decimal('charges', 10, 2)->nullable();
    $table->string('mobile');

    // Status: 1=New, 2=In Progress, 3=Converted, 0=Cancelled
    $table->tinyInteger('status')->default(1);

    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leads');
    }
};
