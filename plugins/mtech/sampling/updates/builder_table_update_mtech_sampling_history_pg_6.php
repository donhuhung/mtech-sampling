<?php namespace Mtech\Sampling\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMtechSamplingHistoryPg6 extends Migration
{
    public function up()
    {
        Schema::table('mtech_sampling_history_pg', function($table)
        {
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
            $table->dateTime('login_time')->default(null)->change();
            $table->dateTime('logout_time')->default(null)->change();
            $table->string('checkin_image', 191)->default('null')->change();
            $table->string('checkout_image', 191)->default('null')->change();
            $table->string('longitude', 191)->default('null')->change();
            $table->string('latitude', 191)->default('null')->change();
        });
    }
    
    public function down()
    {
        Schema::table('mtech_sampling_history_pg', function($table)
        {
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
            $table->dateTime('login_time')->default('NULL')->change();
            $table->dateTime('logout_time')->default('NULL')->change();
            $table->string('checkin_image', 191)->default('\'null\'')->change();
            $table->string('checkout_image', 191)->default('\'null\'')->change();
            $table->string('longitude', 191)->default('NULL')->change();
            $table->string('latitude', 191)->default('NULL')->change();
        });
    }
}
