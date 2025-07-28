<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->string('ip')->unique();
            $table->string('password');
            $table->string('type')->nullable();  
            $table->string('exposed')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('servers');
    }
};
