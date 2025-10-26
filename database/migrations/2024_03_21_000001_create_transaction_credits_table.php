<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // ⚠️ IMPORTANT: Do NOT run migrations on external databases!
        // This migration is for reference only.
        // The transaction_credit table already exists in the live emergency_credit database.
        // This application only READS from external databases and does not modify their structure.
        return;
    }

    public function down()
    {
        Schema::connection('mysql2')->dropIfExists('transaction_credits');
    }
}; 