<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Owl\Repositories\Models\Item;
use Owl\Repositories\Models\ItemHistory;

class CreateItemHistoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('items_history',function($table)
        {
            $table->increments('id');
            $table->integer('item_id');
            $table->string('open_item_id',20);
            $table->integer('user_id');
            $table->string('title',255);
            $table->text('body');
            $table->integer('published')->default(0);
            $table->timestamps();
        });

        $items = Item::all();
        foreach( $items as $item ){
            ItemHistory::insertPastHistory($item);
        }
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('items_history');
	}

}
