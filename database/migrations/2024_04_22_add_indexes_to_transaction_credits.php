<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Check if indexes exist before adding them
        Schema::connection('mysql2')->table('transaction_credits', function (Blueprint $table) {
            // Only add indexes if they don't exist
            if (!$this->indexExists('transaction_credits', 'created_at_status_index')) {
                $table->index(['created_at', 'status'], 'created_at_status_index');
            }
        });
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