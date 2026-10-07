<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void {
        Schema::create('repair_order_parts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('repair_order_id')
                ->constrained('repair_order')
                ->cascadeOnDelete();

            $table->string('name');

            $table->text('description')
                ->nullable();

            $table->decimal('quantity', 10, 2)
                ->default(1);

            $table->decimal('unit_value', 10, 2);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('repair_order_parts');
    }
};
