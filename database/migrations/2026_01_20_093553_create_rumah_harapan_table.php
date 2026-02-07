<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rumah_harapans', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique();
            $table->string('nama', 255);
            $table->text('alamat');
            $table->string('kota', 100);
            $table->string('provinsi', 100);
            $table->string('telepon', 20)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('koordinator', 255)->nullable();
            $table->boolean('is_active')->default(true);

            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rumah_harapans');
    }
};