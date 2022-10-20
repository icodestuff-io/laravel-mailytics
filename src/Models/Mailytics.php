<?php

namespace Icodestuff\Mailytics\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Mailytics extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = Str::uuid()->toString();
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'uuid',
        'mailable_class',
        'subject',
        'sender',
        'recipients',
        'ccs',
        'bccs',
        'image_signature',
        'seen_at',
        'sent_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'recipients' => 'collection',
        'ccs' => 'collection',
        'bccs' => 'collection',
        'sent_at' => 'datetime',
        'seen_at' => 'datetime',
    ];
}
