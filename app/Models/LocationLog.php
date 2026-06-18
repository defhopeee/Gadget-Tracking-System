<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationLog extends Model
{
    protected $fillable = [
        'phone_id',
        'latitude',
        'longitude',
        'accuracy',
        'battery',
        'label',
        'source',
    ];

    protected $casts = [
        'latitude'  => 'float',
        'longitude' => 'float',
        'accuracy'  => 'float',
        'battery'   => 'integer',
    ];

    public function phone()
    {
        return $this->belongsTo(Phone::class);
    }
}
