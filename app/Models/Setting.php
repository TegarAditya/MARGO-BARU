<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\CreatedUpdatedBy;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
    use SoftDeletes, Auditable, HasFactory, CreatedUpdatedBy;

    public $table = 'settings';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'key',
        'value',
        'is_json',
        'created_by_id',
        'updated_by_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function key($key, $fallbackValue = null) {
        $setting = self::where('key', $key)->first();

        if (!$setting) return $fallbackValue;
        if (!$setting->is_json) return $setting->value ?: $fallbackValue;

        $result = $fallbackValue;

        try {
            $result = json_decode($setting->value);
        } catch (\Exception $e) {
            // 
        }

        return $result;
    }

    /**
     * Load all settings with formatted keys and values
     * 
     * @return object `{ "key" => "value" }`
     * 
     * @example string `$settings = Setting::mapWithKeys();`
     */
    public static function mapWithKeys(): Collection
    {
        return self::get()->mapWithKeys(function($item) {
            $value = $item->value;

            if ($item->is_json) {
                try {
                    $value = json_decode($item->value);
                } catch (\Exception $e) {
                    // 
                }
            }

            return [$item->key => $value];
        });
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function update_by()
    {
        return $this->belongsTo(User::class, 'update_by_id');
    }
}
