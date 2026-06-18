<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Phone extends Model
{
    protected $fillable = [
        'user_id',
        'label',
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
