<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void {
        Schema::create('clients_motorcycles', function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId('client_id')
                ->constrained('users_clients')
                ->cascadeOnDelete();

            $table->string('plate');
            $table->string('renavam', 11)->nullable();
            $table->string('chassis', 17)->nullable();

            $table->string('brand');
            $table->string('model');
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('color')->nullable();
            $table->string('engine_number')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('clients_motorcycles');
    }
};
