<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePengaturanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumns('pengaturan', ['nama', 'logo'])) {
            Schema::table('pengaturan', function (Blueprint $table) {
                $table->dropColumn(['nama', 'logo']);
            });
    
            Schema::table('pengaturan', function (Blueprint $table) {
                $table->string('key')->after('id')->default('nama');
                $table->string('name')->after('key')->default('Nama Institusi');
                $table->string('value')->after('name')->default('PAUD TERPADU MUSTIKA ILMU');
            });
        }
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumns('pengaturan', ['key', 'name', 'value'])) {
            Schema::table('pengaturan', function (Blueprint $table) {
                $table->dropColumn(['key', 'name', 'value']);
            });

            Schema::table('pengaturan', function (Blueprint $table) {
                $table->string('nama')->after('id')->default('PAUD TERPADU MUSTIKA ILMU');
                $table->string('logo')->after('nama')->default('logo.png');
            });
        }
    }
}
