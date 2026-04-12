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
        Schema::create('ngtmas', function (Blueprint $table) {
            $table->id();
            $table->integer('no')->nullable();
            $table->string('project')->nullable();
            $table->string('id_lop')->nullable();
            $table->string('cc')->nullable();
            $table->string('nipnas')->nullable();
            $table->string('am')->nullable();
            $table->string('mitra')->nullable();
            $table->integer('plan_bulan_billcomp_2025')->nullable();
            $table->decimal('est_nilai_bc', 20, 2)->nullable();
            $table->unsignedBigInteger('imports_log_id')->nullable();
            $table->boolean('is_manual')->default(false);
            $table->timestamps();
            $table->foreign('imports_log_id')->references('id')->on('scalling_imports')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ngtmas');
    }
};
