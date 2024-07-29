<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait ColumnMigrationFields
{
    public function AddCommonFields($table)
    {
        // $table->boolean('is_active')->default(1)->comment('0 - Deactive, 1 - Activate');
        $table->integer('created_by')->nullable();
        $table->integer('updated_by')->nullable();
        $table->timestamps();
    }

    // public function AddCommonForeignKey($table)
    // {
    //     $table->foreign('created_by')->references('id')->on('tbl_conn_users');
    //     $table->foreign('updated_by')->references('id')->on('tbl_conn_users');
    // }
}
