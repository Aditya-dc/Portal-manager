<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_portal_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('portal_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['user_id', 'portal_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_portal_assignments');
    }
};
