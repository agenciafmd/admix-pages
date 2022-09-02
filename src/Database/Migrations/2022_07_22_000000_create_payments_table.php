<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')
                ->nullable();
            $table->string('code')
                ->nullable();
            $table->boolean('is_active')
                ->default(1);
            $table->string('status')
                ->nullable();
            $table->string('user_id')
                ->nullable();
            $table->string('plan_id')
                ->nullable();
            $table->string('value');
            $table->text('description')
                ->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
