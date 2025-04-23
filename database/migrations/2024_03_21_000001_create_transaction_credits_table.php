<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::connection('mysql2')->create('transaction_credits', function (Blueprint $table) {
            $table->id();
            $table->string('msisdn');
            $table->string('status');
            $table->string('credit_type');
            $table->decimal('units_amount_to_pay', 10, 2);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
        });
    }

    public function down()
    {
        Schema::connection('mysql2')->dropIfExists('transaction_credits');
    }
}; 