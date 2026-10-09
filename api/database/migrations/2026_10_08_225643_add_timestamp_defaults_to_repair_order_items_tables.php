<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void {
        Schema::table('repair_order_parts', function (Blueprint $table) {
            $table->timestamp('created_at')
                ->useCurrent()
                ->change();

            $table->timestamp('updated_at')
                ->useCurrent()
                ->useCurrentOnUpdate()
                ->change();
        });

        Schema::table('repair_order_services', function (Blueprint $table) {
            $table->timestamp('created_at')
                ->useCurrent()
                ->change();

            $table->timestamp('updated_at')
                ->useCurrent()
                ->useCurrentOnUpdate()
                ->change();
        });
    }

    public function down(): void {
        Schema::table('repair_order_parts', function (Blueprint $table) {
            $table->timestamp('created_at')
                ->useCurrent()
                ->change();

            $table->timestamp('updated_at')
                ->useCurrent()
                ->change();
        });

        Schema::table('repair_order_services', function (Blueprint $table) {
            $table->timestamp('created_at')
                ->useCurrent()
                ->change();

            $table->timestamp('updated_at')
                ->useCurrent()
                ->change();
        });
    }
};
