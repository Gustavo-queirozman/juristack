<?php

use App\Models\Enterprise;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enterprises', function (Blueprint $table) {
            if (! Schema::hasColumn('enterprises', 'slug')) {
                $table->string('slug')->nullable()->after('name')->unique();
            }
        });

        Enterprise::query()
            ->whereNull('slug')
            ->orderBy('id')
            ->get()
            ->each(function (Enterprise $enterprise): void {
                $enterprise->forceFill([
                    'slug' => Enterprise::generateUniqueSlug($enterprise->name, $enterprise->id),
                ])->saveQuietly();
            });
    }

    public function down(): void
    {
        Schema::table('enterprises', function (Blueprint $table) {
            if (Schema::hasColumn('enterprises', 'slug')) {
                $table->dropUnique('enterprises_slug_unique');
                $table->dropColumn('slug');
            }
        });
    }
};
