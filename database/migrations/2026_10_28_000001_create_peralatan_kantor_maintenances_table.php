<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peralatan_kantor_maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peralatan_kantor_id')->constrained('peralatan_kantor')->cascadeOnDelete();
            $table->date('maintenance_date');
            $table->text('description');
            $table->decimal('cost', 15, 2)->nullable();
            $table->string('vendor')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['peralatan_kantor_id', 'maintenance_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peralatan_kantor_maintenances');
    }
};
