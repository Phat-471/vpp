<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'description',
    ];

    /**
     * Clear settings cache on save/delete
     */
    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('app_settings_all');
        });

        static::deleted(function () {
            Cache::forget('app_settings_all');
        });
    }

    /**
     * Quick helper to get setting value
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = Cache::remember('app_settings_all', 3600, function () {
            return static::pluck('value', 'key')->toArray();
        });

        return $settings[$key] ?? $default;
    }

    /**
     * Quick helper to set setting value
     */
    public static function set(string $key, mixed $value, string $group = 'general', string $type = 'text', ?string $description = null): static
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : (string) $value,
                'group' => $group,
                'type' => $type,
                'description' => $description,
            ]
        );

        Cache::forget('app_settings_all');

        return $setting;
    }
}
