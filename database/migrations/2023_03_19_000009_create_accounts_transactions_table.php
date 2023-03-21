<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('cashbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->timestamps();
        });
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('accountable_type');
            $table->unsignedBigInteger('accountable_id');
            $table->unique(['accountable_type', 'accountable_id']);
            $table->decimal('balance', 21, 11)->default(0.00);
            $table->timestamps();
        });
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_id');
            $table->unsignedBigInteger('to_id')->nullable();
            $table->decimal('amount', 21, 11);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('from_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('to_id')->references('id')->on('accounts')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('partners');
        Schema::dropIfExists('cashbacks');
        Schema::dropIfExists('sites');
    }
};
