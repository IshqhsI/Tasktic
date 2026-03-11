<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'dosen', 'mahasiswa'])->default('mahasiswa')->after('email');
            $table->string('nim_nidn')->nullable()->after('role');
            $table->foreignId('prodi_id')->nullable()->constrained('prodi')->nullOnDelete()->after('nim_nidn');
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete()->after('prodi_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['prodi_id']);
            $table->dropForeign(['kelas_id']);
            $table->dropColumn(['role', 'nim_nidn', 'prodi_id', 'kelas_id']);
        });
    }

};
