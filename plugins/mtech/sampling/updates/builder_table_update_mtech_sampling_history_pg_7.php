<?php namespace Mtech\Sampling\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMtechSamplingHistoryPg7 extends Migration
{
    public function up()
    {
        Schema::table('mtech_sampling_history_pg', function($table)
        {
            $table->string('longitude_checkin', 191)->nullable();
            $table->string('latitude_chekin', 191)->nullable();
            $table->string('longitude_checkout')->nullable();
            $table->string('latitude_checkout')->nullable();
            $table->dateTime('login_time')->default(null)->change();
            $table->dateTime('logout_time')->default(null)->change();
            $table->string('checkin_image', 191)->default(null)->change();
            $table->string('checkout_image', 191)->default(null)->change();
            $table->dropColumn('longitude');
            $table->dropColumn('latitude');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
    
    public function down()
    {
        Schema::table('mtech_sampling_history_pg', function($table)
        {
            $table->dropColumn('longitude_checkin');
            $table->dropColumn('latitude_chekin');
            $table->dropColumn('longitude_checkout');
            $table->dropColumn('latitude_checkout');
            $table->dateTime('login_time')->default('NULL')->change();
            $table->dateTime('logout_time')->default('NULL')->change();
            $table->string('checkin_image', 191)->default('\'null\'')->change();
            $table->string('checkout_image', 191)->default('\'null\'')->change();
            $table->string('longitude', 191)->nullable()->default('\'null\'');
            $table->string('latitude', 191)->nullable()->default('\'null\'');
            $table->timestamp('created_at')->default('current_timestamp()');
            $table->timestamp('updated_at')->nullable()->default('NULL');
        });
    }
}
