<?php namespace RainLab\User\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class UsersAddCountryId extends Migration
{
    public function up()
    {
        Schema::table('users', function($table)
        {            
            $table->integer('country_id');            
            $table->integer('is_block');            
        });
    }

    public function down()
    {
        if (Schema::hasColumn('users', 'country_id')) {
            Schema::table('users', function($table)
            {
                $table->dropColumn('country_id');
                $table->dropColumn('is_block');
            });
        }
    }
}
