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
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('bengkel_id')->nullable()->after('payment_method_id')->constrained('bengkels')->nullOnDelete();
            $table->string('billing_status')->nullable()->default('belum_ditagih')->after('bengkel_id'); // belum_ditagih, sudah_ditagih, sudah_dibayar
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['bengkel_id']);
            $table->dropColumn(['bengkel_id', 'billing_status']);
        });
    }
};
