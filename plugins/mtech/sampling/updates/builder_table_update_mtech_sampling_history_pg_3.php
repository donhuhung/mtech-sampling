<?php namespace Mtech\Sampling\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMtechSamplingHistoryPg3 extends Migration
{
    public function up()
    {
        Schema::table('mtech_sampling_history_pg', function($table)
        {
            $table->string('checkin_image')->nullable();
            $table->string('checkout_image')->nullable();
            $table->dateTime('login_time')->nullable()->change();
            $table->dateTime('logout_time')->nullable()->change();
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
    
    public function down()
    {
        Schema::table('mtech_sampling_history_pg', function($table)
        {
            $table->dropColumn('checkin_image');
            $table->dropColumn('checkout_image');
            $table->dateTime('login_time')->nullable(false)->change();
            $table->dateTime('logout_time')->nullable(false)->change();
            $table->timestamp('created_at')->default('current_timestamp()');
            $table->timestamp('updated_at')->nullable()->default('NULL');
        });
    }
}
