<?php namespace Mtech\Sampling\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateCustomerGiftExportsTable extends Migration
{
    public function up()
    {
        Schema::create('mtech_sampling_customer_gift_exports', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mtech_sampling_customer_gift_exports');
    }
}
