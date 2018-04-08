<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('reg_token')->nullable();
            $table->string('api_header')->nullable();
            $table->string('fcode')->nullable(); //fcode
            $table->string('dep')->nullable();
            $table->string('job')->nullable();
            $table->string('nat_id')->nullable();
            $table->string('mobile')->nullable();
            $table->string('gender')->nullable();
            $table->string('gov')->nullable();
            $table->string('branch')->nullable();
            $table->string('elec_branch')->nullable();
            $table->string('graduate')->nullable();
            $table->string('pic')->nullable();
            $table->string('members_Type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('members');
    }
}
