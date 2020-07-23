<?php namespace Mtech\Sampling\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMtechSamplingHistoryPg5 extends Migration
{
    public function up()
    {
        Schema::table('mtech_sampling_history_pg', function($table)
        {
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->dateTime('login_time')->default(null)->change();
            $table->dateTime('logout_time')->default(null)->change();
            $table->string('checkin_image', 191)->default('null')->change();
            $table->string('checkout_image', 191)->default('null')->change();
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
    
    public function down()
    {
        Schema::table('mtech_sampling_history_pg', function($table)
        {
            $table->dropColumn('longitude');
            $table->dropColumn('latitude');
            $table->dateTime('login_time')->default('NULL')->change();
            $table->dateTime('logout_time')->default('NULL')->change();
            $table->string('checkin_image', 191)->default('\'null\'')->change();
            $table->string('checkout_image', 191)->default('\'null\'')->change();
            $table->timestamp('created_at')->default('current_timestamp()');
            $table->timestamp('updated_at')->nullable()->default('NULL');
        });
    }
}
