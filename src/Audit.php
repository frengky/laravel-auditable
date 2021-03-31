<?php

namespace Frengky\Auditable;

use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    protected $guarded = [];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
        'created_at' => 'datetime'
    ];

    public function auditable()
    {
        return $this->morphTo();
    }
}