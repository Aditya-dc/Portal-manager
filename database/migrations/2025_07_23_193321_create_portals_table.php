<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('portals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('url');
            $table->string('developed_by');
            $table->boolean('vapt')->default(false);
            $table->boolean('backup')->default(false);
            $table->enum('status', ['up', 'down'])->default('down');
            $table->timestamp('last_checked')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('portals');
    }
};
