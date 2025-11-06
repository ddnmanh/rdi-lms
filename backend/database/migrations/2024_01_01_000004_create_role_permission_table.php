<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolePermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_permission', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_id');
            $table->timestamps();
            
            $table->unique(['role_id', 'permission_id'], 'uq_role_perm');
            $table->foreign('role_id', 'fk_rp_role')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');
            $table->foreign('permission_id', 'fk_rp_perm')
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade');
            
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_0900_ai_ci';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_permission');
    }
}

