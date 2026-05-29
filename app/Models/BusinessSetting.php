<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class BusinessSetting extends Model
{
    protected $table = 'business_settings';

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'value' => 'json'
    ];

    /**
     * Get a setting value by key
     */
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        $value = $setting->value;

        // Decode based on type
        switch ($setting->type) {
            case 'json':
                return json_decode($value, true);
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'file':
                return $value ? asset('storage/' . $value) : null;
            default:
                return $value;
        }
    }

    /**
     * Set a setting value
     */
    public static function set($key, $value, $type = 'text', $group = 'general')
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : $value,
                'type' => $type,
                'group' => $group
            ]
        );

        // Clear cache
        Cache::forget('business_settings');

        return $setting;
    }

    /**
     * Get all settings grouped
     */
    public static function getAllGrouped()
    {
        $settings = Cache::remember('business_settings', 3600, function () {
            return self::orderBy('sort_order')->get();
        });

        return $settings->groupBy('group');
    }

    /**
     * Get company logo URL
     */
    public static function getLogo()
    {
        $logo = self::get('company_logo');
        return $logo ?: asset('images/default-logo.png');
    }

    /**
     * Get company stamp URL
     */
    public static function getStamp()
    {
        $stamp = self::get('company_stamp');
        return $stamp ?: null;
    }

    /**
     * Get contact information
     */
    public static function getContactInfo()
    {
        return [
            'phone' => self::get('phone'),
            'phone_alternative' => self::get('phone_alternative'),
            'email' => self::get('email'),
            'email_alternative' => self::get('email_alternative'),
            'address' => self::get('address'),
            'city' => self::get('city'),
            'country' => self::get('country'),
            'postal_code' => self::get('postal_code'),
            'facebook' => self::get('facebook'),
            'twitter' => self::get('twitter'),
            'instagram' => self::get('instagram'),
            'linkedin' => self::get('linkedin'),
        ];
    }

    /**
     * Get SMTP mail configuration
     */
    public static function getMailConfig()
    {
        return [
            'mailer' => self::get('mail_mailer', 'smtp'),
            'host' => self::get('mail_host', 'smtp.gmail.com'),
            'port' => self::get('mail_port', 587),
            'username' => self::get('mail_username'),
            'password' => self::get('mail_password'),
            'encryption' => self::get('mail_encryption', 'tls'),
            'from_address' => self::get('mail_from_address'),
            'from_name' => self::get('mail_from_name'),
        ];
    }

    /**
     * Get locations/branches
     */
    public static function getLocations()
    {
        return self::get('locations', []);
    }
}
