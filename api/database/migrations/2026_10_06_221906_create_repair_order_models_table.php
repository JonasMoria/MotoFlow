<?php

use App\Enums\RepairStatusCode;
use App\Enums\UrgencyLevelCode;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void {
        Schema::create('repair_order', function (Blueprint $table) {
            $table->id();

            $table->foreignId('motorcycle_id')
                ->constrained('clients_motorcycles')
                ->cascadeOnDelete();

            $table->unsignedSmallInteger('status')
                ->default(RepairStatusCode::CREATED->value)
                ->index();

            $table->unsignedSmallInteger('urgency_level')
                ->default(UrgencyLevelCode::LOW->value)
                ->index();

            $table->string('title');

            $table->text('description');

            $table->text('diagnosis')
                ->nullable();

            $table->text('observations')
                ->nullable();

            $table->timestamp('started_at')
                ->nullable();

            $table->timestamp('finished_at')
                ->nullable();

            $table->timestamp('delivered_at')
                ->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('repair_order');
    }
};
