<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->text('exposed')->nullable()->change(); // Change to text to allow longer custom values
        });
    }

    public function down()
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->string('exposed')->nullable()->change();
        });
    }
};
