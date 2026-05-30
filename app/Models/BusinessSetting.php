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
        // ✅ 'value' cast REMOVED — we handle encoding/decoding manually
        //    to avoid double-encoding conflicts
    ];

    /**
     * Get a setting value by key.
     * Always reads raw DB value to avoid cast interference.
     */
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        // Use getRawOriginal so the (now-removed) cast never interferes
        $raw = $setting->getRawOriginal('value');

        if (is_null($raw)) {
            return $default;
        }

        switch ($setting->type) {
            case 'json':
                $decoded = json_decode($raw, true);
                return $decoded !== null ? $decoded : $default;

            case 'boolean':
                $decoded = json_decode($raw);
                return filter_var($decoded, FILTER_VALIDATE_BOOLEAN);

            case 'file':
                // Raw value is stored as a JSON-encoded string: "settings/logos/abc.jpg"
                // json_decode strips the surrounding quotes → settings/logos/abc.jpg
                $path = json_decode($raw);
                return $path ? asset('storage/' . $path) : null;

            default:
                // Plain text values are also JSON-encoded strings in the DB
                $decoded = json_decode($raw, true);
                return $decoded !== null ? $decoded : $raw;
        }
    }

    /**
     * Set a setting value.
     * All values are JSON-encoded for consistency.
     */
    public static function set($key, $value, $type = 'text', $group = 'general')
    {
        if (is_null($value)) {
            $encoded = null;
        } elseif (is_array($value)) {
            // Arrays encoded once — no cast will re-encode
            $encoded = json_encode($value);
        } else {
            // Strings/numbers encoded as JSON strings: "my value"
            $encoded = json_encode($value);
        }

        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $encoded,
                'type'  => $type,
                'group' => $group,
            ]
        );

        Cache::forget('business_settings');

        return $setting;
    }

    /**
     * Get all settings grouped by group key.
     */
    public static function getAllGrouped()
    {
        $settings = Cache::remember('business_settings', 3600, function () {
            return self::orderBy('sort_order')->get();
        });

        return $settings->groupBy('group');
    }

    /**
     * Get company logo URL.
     * Returns the asset URL or a default placeholder.
     */
    public static function getLogo(): ?string
    {
        $url = self::get('company_logo');
        return $url ?: asset('images/default-logo.png');
    }

    /**
     * Get company stamp URL.
     */
    public static function getStamp(): ?string
    {
        return self::get('company_stamp');
    }

    /**
     * Get contact information as an array.
     */
    public static function getContactInfo(): array
    {
        return [
            'phone'             => self::get('phone'),
            'phone_alternative' => self::get('phone_alternative'),
            'email'             => self::get('email'),
            'email_alternative' => self::get('email_alternative'),
            'address'           => self::get('address'),
            'city'              => self::get('city'),
            'country'           => self::get('country'),
            'postal_code'       => self::get('postal_code'),
            'facebook'          => self::get('facebook'),
            'twitter'           => self::get('twitter'),
            'instagram'         => self::get('instagram'),
            'linkedin'          => self::get('linkedin'),
        ];
    }

    /**
     * Get SMTP mail configuration.
     */
    public static function getMailConfig(): array
    {
        return [
            'mailer'       => self::get('mail_mailer',       'smtp'),
            'host'         => self::get('mail_host',         'smtp.gmail.com'),
            'port'         => self::get('mail_port',         587),
            'username'     => self::get('mail_username'),
            'password'     => self::get('mail_password'),
            'encryption'   => self::get('mail_encryption',   'tls'),
            'from_address' => self::get('mail_from_address'),
            'from_name'    => self::get('mail_from_name'),
        ];
    }

    /**
     * Get locations / branches.
     */
    public static function getLocations(): array
    {
        return self::get('locations', []) ?: [];
    }
}
