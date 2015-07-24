<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonkeySettingsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	//create settings
    	if (!Schema::hasTable('settings'))
    	{
    		Schema::create('settings', function(Blueprint $table) {
	    		$table->increments('id');
	    		$table->string('name');
	    		$table->string('type');
	    		$table->string('default_value');
	    	});	
    	} 
    	else 
    	{
    		$hasNeededColumns = 
    			Schema::hasColumn('settings', 'id') &&
    			Schema::hasColumn('settings', 'name') &&
    			Schema::hasColumn('settings', 'type') &&
    			Schema::hasColumn('settings', 'default_value');

    		if(!$hasNeededColumns)
    			return "Table with name 'settings' is not compatible with MonkeySettings, sorry.";
    	}
    	

    	if(!Schema::hasTable('user_settings'))
    	{
    		$usersTableName = "";
    		if(Schema::hasTable('users'))
    		{
    			$usersTableName = 'users';
    		} 
    		else if(Schema::hasTable('user'))
    		{
    			$usersTableName = 'user';
    		}
    		else if(Schema::hasTable('system_user'))
    		{
    			$usersTableName = 'system_user';
    		}
    		else 
    		{
    			return "User table not found, but necessary for MonkeySettings, sorry.";
    		}

            $usersIdName = "";
            if(Schema::hasColumn($usersTableName, 'id'))
            {
                $usersIdName = "id";
            }
            else if(Schema::hasColumn($usersTableName, 'user_id'))
            {
                $usersIdName = "user_id";
            }
            else
            {
                return "ID column in user table " . $usersTableName . " not found.";
            }

    		Schema::create('user_settings', function(Blueprint $table) use($usersTableName, $usersIdName) {
                $table->increments('id');
	    		$table->integer('user_id')->unsigned();
                $table->integer('setting_id')->unsigned();
	    		$table->text('value')->nullable();

	    		//$table->foreign('user_id')->references($usersIdName)->on($usersTableName);
                $table->foreign('setting_id')->references('id')->on('settings');
	    	});
    	}
    	else
    	{
    		$hasNeededColumns = 
    			Schema::hasColumn('user_settings', 'id') &&
    			Schema::hasColumn('user_settings', 'user_id') &&
    			Schema::hasColumn('user_settings', 'setting_id') &&
    			Schema::hasColumn('user_settings', 'value');

    		if(!$hasNeededColumns)
    			return "Table with name 'user_settings' is not compatible with MonkeySettings, sorry.";
    	}
    	
    	if(!Schema::hasTable('system_settings'))
    	{
    		Schema::create('system_settings', function(Blueprint $table) {
                $table->integer('setting_id')->unsigned();
	    		$table->text('value')->nullable();

	    		$table->primary('setting_id');
                $table->foreign('setting_id')->references('id')->on('settings');
	    	});
    	}
    	else
    	{
    		$hasNeededColumns = 
    			Schema::hasColumn('system_settings', 'setting_id') &&
    			Schema::hasColumn('system_settings', 'value');

    		if(!$hasNeededColumns)
    			return "Table with name 'system_settings' is not compatible with MonkeySettings, sorry.";
    	}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('user_settings');
        Schema::dropIfExists('settings');
    }
}