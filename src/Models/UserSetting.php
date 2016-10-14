<?php
namespace Designitgmbh\MonkeySettings\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

use App\Models\User;

class UserSetting extends Model {
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'setting_id', 'value'];
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
	public $timestamps = false;
	
	public static function instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new UserSetting;
        }
        return $inst;
    }

    public function user()
	{
		return $this->hasOne('User');
	}

	public function setting()
	{
		return $this->hasOne('Setting');
	}

	/**
     * Get a a profile setting.
     *
     * @param  string   $key_string  => key of the desired setting
     * @param  int 		$user_id 	 => id of the desired user (if not provided it will get the id from the logged user)
     * @param  boolean  $returnValue => if true => return the value of the setting, if false => return the setting model (UserSetting)
     * @return string or UserSetting => user setting value or an instance of UserSetting
     */
	public static function get($key_string, $user_id = 0, $returnValue = true)
	{
		if ($user_id == 0) {
			if(Auth::getUser()) {
				$user_id = Auth::getUser()->id;	
			} else {
				return null;
			}			
		}

		$setting = Setting::get($key_string);
		$setting_id = $setting->id;
		
		$userSetting = UserSetting::where('user_id', '=', $user_id)->where('setting_id', '=', $setting_id)->get()->first();
		if (!$userSetting)
		{
			UserSetting::create([
				"user_id" => $user_id,
				"setting_id" => $setting_id,
				"value" => ''
			]);

			$userSetting = UserSetting::where('user_id', '=', $user_id)->where('setting_id', '=', $setting_id)->get()->first();
		}
		
		return $returnValue ? $userSetting->value : $userSetting;
	}

	/**
     * Get a a profile setting.
     *
     * @param  string  $key_string 	=> key of the desired setting
     * @param  string  $value 		=> value to be set to the setting
     * @param  int 	   $user_id 	=> id of the desired user (if not provided it will get the id from the logged user)
     * @return boolean 				=> true if correctly saved, false otherwise
     */
	public static function set($key_string, $value, $user_id = 0)
	{
		if ($user_id == 0) {
			if(Auth::getUser()) {
				$user_id = Auth::getUser()->id;	
			} else {
				return null;
			}			
		}
		
		$setting = Setting::get($key_string);
		$setting_id = $setting->id;

		$userSetting = UserSetting::where('user_id', '=', $user_id)->where('setting_id', '=', $setting_id)->get()->first();
		if($userSetting)
		{
			$userSetting->value = $value;
			$userSetting->save();
		}
		else
		{
			return UserSetting::create([
				"user_id" => $user_id,
				"setting_id" => $setting_id,
				"value" => $value
			]);
		}
	}
	
}