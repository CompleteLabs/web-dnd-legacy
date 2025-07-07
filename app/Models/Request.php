<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Request extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [
        'id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id')->withTrashed();
    }

    public function approveId()
    {
        return $this->belongsTo(User::class, 'approval_id')->withTrashed();
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by')->withTrashed();
    }

    protected function createdAt(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function ($value) {
            if ($value) {
                return Carbon::parse($value)->getPreciseTimestamp(3);
            }
        });
    }
    protected function updatedAt(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function ($value) {
            if ($value) {
                return Carbon::parse($value)->getPreciseTimestamp(3);
    
            }
        });
    }
    protected function approvedAt(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function ($value) {
            if ($value) {
                return Carbon::parse($value)->getPreciseTimestamp(3);
            }
        });
    }

    protected function deletedAt(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function ($value) {
            if ($value) {
                return Carbon::parse($value)->getPreciseTimestamp(3);
            }
        });
    }
}
