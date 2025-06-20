<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'vehicle_id',
        'usage_type',
        'service_type',
        'service_date',
        'next_service_date',
        'next_service_km',
        'start_km',
        'end_km',
        'fuel_used',
        'service_cost',
        'service_vendor',
        'notes'
    ];

    protected $casts = [
        'service_date' => 'date',
        'next_service_date' => 'date',
        'service_cost' => 'decimal:2',
        'fuel_used' => 'decimal:2'
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getDistanceAttribute(): int
    {
        return $this->end_km - $this->start_km;
    }

    public function getFuelEfficiencyAttribute(): float
    {
        if ($this->fuel_used == 0 || $this->usage_type !== 'trip') {
            return 0;
        }
        return $this->distance / $this->fuel_used;
    }

    // Scope for trip records
    public function scopeTrips($query)
    {
        return $query->where('usage_type', 'trip');
    }

    // Scope for service records
    public function scopeServices($query)
    {
        return $query->whereIn('usage_type', ['service', 'maintenance']);
    }

    // Scope for upcoming services
    public function scopeUpcomingServices($query)
    {
        return $query->whereIn('usage_type', ['service', 'maintenance'])
            ->whereNotNull('next_service_date')
            ->where('next_service_date', '>=', now());
    }
}