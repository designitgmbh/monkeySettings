<?php
namespace Designitgmbh\MonkeySettings\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model {
    protected $primaryKey = 'setting_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['setting_id', 'value'];
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
	public $timestamps = false;

    public function setting() {
        return $this->belongsTo('Designitgmbh\MonkeySettings\Models\Setting');
    }

    private static function getSetting($key) {
        $systemSetting = self::whereHas('setting', function($query) use ($key) {
            $query->where('name', $key);
        });

        if ($systemSetting->count() == 1)
            return $systemSetting->get()->first();

        return null;
    }

    public static function get($key) {
        $value = null;
        $type = null;
        $systemSetting = SystemSetting::getSetting($key);

        if($systemSetting != null) {
            $value = $systemSetting->value;
            $type = $systemSetting->setting->type;
        }
        if (is_null($value)) {
            // Either value is null or there is no global setting at all. Look for the setting
            $setting = Setting::where('name', $key);
            if ($setting->count() == 0) {
                throw new Exception("Setting $key does not exist");
            }
            $setting = $setting->get()->first();
            $value = $setting->default_value;
            $type = $setting->type;
        }
        // Do some conversions
        switch ($type) {
            case 'boolean':
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                break;
        }
        return $value;
    }

    public static function set($key, $value) {
        if($oldSystemSetting = SystemSetting::getSetting($key)) {
            $setting = $oldSystemSetting->setting()->first();
            $oldSystemSetting->delete();
        } else {
            $setting = Setting::get($key);    
        }
        
        $settingId = $setting->id;

        return SystemSetting::create([
            "value"         => $value,
            "setting_id"    => $settingId
        ]);
    }
}