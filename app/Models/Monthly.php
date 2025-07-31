<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Monthly extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = [
        'id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    protected function date(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function ($value) {
            return Carbon::parse($value)->getPreciseTimestamp(3);
        });
    }

    protected function createdAt(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function ($value) {
            return Carbon::parse($value)->getPreciseTimestamp(3);
        });
    }

    protected function updatedAt(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function ($value) {
            return Carbon::parse($value)->getPreciseTimestamp(3);
        });
    }

    public function add()
    {
        return $this->belongsTo(User::class, 'add_id')->withTrashed();
    }

    public function tag()
    {
        return $this->belongsTo(User::class, 'tag_id')->withTrashed();
    }
}
