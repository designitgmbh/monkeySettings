<?php
namespace Designitgmbh\MonkeySettings\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model {
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	public $table = 'settings';
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];
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
            $inst = new Setting;
        }
        return $inst;
    }

    public static function get($name)
	{
		$setting = Setting::where('name', '=', $name)->get()->first();
		if (!$setting)
		{
			$setting = new Setting(array('name' => $name));
			$setting->save();
		}
		return $setting;
	}
    
}