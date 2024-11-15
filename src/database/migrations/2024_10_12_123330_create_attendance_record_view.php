<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('
            CREATE OR REPLACE VIEW attendance_record_view AS SELECT
                U.id,
                U.name,
                W.date,
                W.work_start,
                W.work_end,
                SEC_TO_TIME(COALESCE(SUM(TIME_TO_SEC(TIMEDIFF(R.rest_end,R.rest_start))),0)) AS rest_total,
                TIMEDIFF(W.work_end,W.work_start) AS work_total,
                U.status
            FROM
                users AS U
            JOIN
                works AS W ON U.id = W.user_id
            LEFT JOIN
                rests AS R ON W.id = R.work_id
            GROUP BY
                W.id
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_record_view');
    }
};
