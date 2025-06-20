<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $fillable = [
        'booking_number',
        'user_id',
        'vehicle_id',
        'driver_id',
        'purpose',
        'destination',
        'start_date',
        'end_date',
        'status',
        'estimated_km',
        'estimated_fuel'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'estimated_km' => 'integer',
        'estimated_fuel' => 'decimal:2'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            $booking->booking_number = 'BK' . date('Ymd') . str_pad(Booking::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(Approval::class);
    }

    public function vehicleUsage(): HasOne
    {
        return $this->hasOne(VehicleUsage::class);
    }

    public function isFullyApproved(): bool
    {
        return $this->approvals()->where('status', 'approved')->count() >= 2;
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed' || $this->vehicleUsage()->exists();
    }

    public function canBeUsed(): bool
    {
        return $this->status === 'approved' && !$this->vehicleUsage()->exists();
    }

    public function getDurationInHoursAttribute(): float
    {
        return $this->start_date->diffInHours($this->end_date);
    }

    public function getDurationInDaysAttribute(): float
    {
        return $this->start_date->diffInDays($this->end_date);
    }
}