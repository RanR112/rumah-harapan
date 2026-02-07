<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anak_asuhs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rumah_harapan_id')
                ->constrained('rumah_harapans')
                ->onDelete('restrict');
            $table->string('nama_anak', 255);
            $table->string('nik', 16)->unique();
            $table->string('no_kartu_keluarga', 16); // tidak unique!
            $table->text('alamat_lengkap');
            $table->enum('jenis_kel', ['L', 'P']);
            $table->string('tempat_lahir', 100);
            $table->date('tanggal_lahir');
            $table->enum('status', ['aktif', 'tidak aktif']);
            $table->enum('grade', ['A', 'B', 'C', 'D', 'E']);
            $table->string('pendidikan_kelas', 50)->nullable();
            $table->string('nama_orang_tua', 255);
            $table->string('no_handphone', 20)->nullable();
            $table->date('tanggal_masuk_rh');
            $table->string('yang_mengasuh_sebelum_diasrama', 255)->nullable();
            $table->text('rekomendasi')->nullable();

            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');

            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'grade']);
            $table->index('tanggal_masuk_rh');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anak_asuhs');
    }
};
