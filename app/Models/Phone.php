<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Phone extends Model
{
    /** Supported gadget types: key => [label, Bootstrap icon name]. */
    public const TYPES = [
        'phone'       => ['Phone', 'phone'],
        'tablet'      => ['Tablet', 'tablet'],
        'laptop'      => ['Laptop', 'laptop'],
        'smartwatch'  => ['Smartwatch', 'smartwatch'],
        'gps_tracker' => ['GPS tracker', 'geo-alt'],
        'camera'      => ['Camera', 'camera'],
        'other'       => ['Other gadget', 'cpu'],
    ];

    protected $fillable = [
        'user_id',
        'label',
        'type',
        'brand',
        'model',
        'imei',
        'phone_number',
        'owner_name',
        'status',
        'api_key',
    ];

    /**
     * Assign a fresh API key automatically when a phone is created.
     */
    protected static function booted(): void
    {
        static::creating(function (Phone $phone) {
            if (empty($phone->api_key)) {
                $phone->api_key = self::generateApiKey();
            }
        });
    }

    public static function generateApiKey(): string
    {
        return Str::lower(Str::random(32));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function locations()
    {
        return $this->hasMany(LocationLog::class)->latest();
    }

    /** Most recent reported location, if any. */
    public function latestLocation()
    {
        return $this->hasOne(LocationLog::class)->latestOfMany();
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type][0] ?? ucfirst((string) $this->type);
    }

    public function typeIcon(): string
    {
        return 'bi-' . (self::TYPES[$this->type][1] ?? 'cpu');
    }

    public function statusColor(): string
    {
        return [
            'active'    => 'success',
            'inactive'  => 'secondary',
            'lost'      => 'warning',
            'stolen'    => 'danger',
            'recovered' => 'info',
        ][$this->status] ?? 'secondary';
    }
}
