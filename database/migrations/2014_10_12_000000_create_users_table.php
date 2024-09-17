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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->date('tanggal_lahir')->nullable(); // Tambahkan kolom tanggal lahir
            $table->unsignedBigInteger('referal_id')->nullable(); // Tambahkan kolom referal
            $table->string('nomor_hp')->nullable(); // Tambahkan kolom nomor HP
            $table->text('alamat')->nullable(); // Tambahkan kolom alamat

            $table->rememberToken();
            $table->timestamps();
            
            // Definisikan foreign key untuk referal_id, refer ke id pada tabel users
            $table->foreign('referal_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referal_id']); // Hapus foreign key referal_id jika rollback
        });

        Schema::dropIfExists('users');
    }
};
