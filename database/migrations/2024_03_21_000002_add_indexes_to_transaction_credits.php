<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::connection('mysql2')->table('transaction_credits', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('msisdn');
            $table->index('status');
            $table->index('credit_type');
        });
    }

    public function down()
    {
        Schema::connection('mysql2')->table('transaction_credits', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['msisdn']);
            $table->dropIndex(['status']);
            $table->dropIndex(['credit_type']);
        });
    }
}; 