<?php
// database/migrations/2025_09_10_000001_add_type_and_metadata_to_products_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'type')) {
                $table->string('type')->default('physical');
            }
            if (!Schema::hasColumn('products', 'metadata')) {
                $table->json('metadata')->nullable();
            }
        });
    }

    public function down(): void {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'metadata')) {
                $table->dropColumn('metadata');
            }
            if (Schema::hasColumn('products', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};
?>