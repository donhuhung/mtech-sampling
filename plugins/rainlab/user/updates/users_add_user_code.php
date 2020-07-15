<?php namespace RainLab\User\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class UsersAddUserCode extends Migration
{
    public function up()
    {
        Schema::table('users', function($table)
        {
            $table->string('eth_adress')->nullable();
            $table->integer('daily');
            $table->integer('lending');
            $table->integer('zlliqa');
        });
    }

    public function down()
    {
        if (Schema::hasColumn('users', 'user_code')) {
            Schema::table('users', function($table)
            {
                $table->dropColumn('user_code');                
                $table->dropColumn('eth_adress');
                $table->dropColumn('daily');                
                $table->dropColumn('lending');
            });
        }
    }
}
