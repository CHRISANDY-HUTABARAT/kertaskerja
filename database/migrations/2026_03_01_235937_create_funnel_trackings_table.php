<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funnel_tracking', function (Blueprint $table) {
            $table->id();
            $table->string('data_type'); // 'on_hand', 'qualified', 'koreksi', 'initiate'
            $table->unsignedBigInteger('data_id'); // ID dari tabel data yang terkait
            $table->unsignedBigInteger('ngtma_id'); // ID dari tabel NGTMA yang terkait

            // F0 - Lead
            // $table->boolean('f0_lead')->default(false);
            $table->boolean('f0_inisiasi_solusi')->default(false);
            // $table->boolean('f0_technical_proposal')->default(false);
            
            // F1 - Opportunity
            $table->boolean('f1_tech_budget')->default(false);
            
            // F2 - Self Assessment & Management Solution
            $table->boolean('f2_p0_p1')->default(false);
            $table->boolean('f2_p2')->default(false);
            $table->boolean('f2_p3')->default(false);
            $table->boolean('f2_p4')->default(false);
            $table->boolean('f2_offering')->default(false);
            $table->boolean('f2_p5')->default(false);
            $table->boolean('f2_proposal')->default(false);
            
            // F3 - Project Assessment (RPA)
            $table->boolean('f3_p6')->default(false);
            $table->boolean('f3_p7')->default(false);
            $table->boolean('f3_submit')->default(false);
            
            // F4 - Negosiasi
            $table->boolean('f4_negosiasi')->default(false);
            
            // F5 - Win
            $table->boolean('f5_sk_mitra')->default(false);
            $table->boolean('f5_ttd_kontrak')->default(false);
            $table->boolean('f5_p8')->default(false);
            
            // DELIVERY
            $table->boolean('delivery_kontrak')->default(false);
            $table->string('delivery_baut_bast')->nullable();
            $table->string('delivery_baso')->nullable();
            $table->string('delivery_billing_complete')->nullable();
            $table->string('delivery_nilai_billcomp')->nullable();
            
            //CANCEL
            $table->boolean('cancel')->default(false);
            
            $table->timestamps();
            
            // Index untuk query lebih cepat
            $table->index(['data_type', 'data_id', 'ngtma_id']);
            $table->foreign('data_id')->references('id')->on('scalling_data')->onDelete('cascade');
            $table->foreign('ngtma_id')->references('id')->on('ngtmas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funnel_tracking');
    }
};
