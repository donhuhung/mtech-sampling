<?php namespace RainLab\User\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class UsersAddUserGender extends Migration
{
    public function up()
    {
        Schema::table('users', function($table)
        {
            $table->string('gender')->nullable();
            $table->string('dob')->nullable();
        });
    }

    public function down()
    {
        if (Schema::hasColumn('users', 'gender')) {
            Schema::table('users', function($table)
            {
                $table->dropColumn('gender');
                $table->dropColumn('dob');
            });
        }
    }
}
