<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
        Schema::connection('mysql2')->table('transaction_credits', function (Blueprint $table) {
            $table->dropIndex('created_at_status_index');
        });
    }

    private function indexExists($table, $index)
    {
        $conn = DB::connection('mysql2');
        $dbName = $conn->getDatabaseName();
        
        $result = $conn->select("
            SELECT COUNT(*) as count
            FROM information_schema.statistics
            WHERE table_schema = ?
            AND table_name = ?
            AND index_name = ?
        ", [$dbName, $table, $index]);
        
        return $result[0]->count > 0;
    }
}; 