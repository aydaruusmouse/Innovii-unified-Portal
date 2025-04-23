<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::connection('mysql2')->hasTable('transaction_credits')) {
            Schema::connection('mysql2')->create('transaction_credits', function (Blueprint $table) {
                $table->id();
                $table->string('msisdn');
                $table->decimal('units_amount_to_pay', 10, 2);
                $table->string('status');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                
                // Add initial indexes
                $table->index(['created_at', 'status']);
                $table->index('msisdn');
                $table->index('units_amount_to_pay');
            });
        }
    }

    public function down()
    {
        Schema::connection('mysql2')->dropIfExists('transaction_credits');
    }
}; 