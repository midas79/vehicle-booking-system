<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'license_plate',
        'type',
        'ownership',
        'fuel_consumption',
        'service_schedule_days',
        'last_service_date',
        'status',
        'region_id',
        'service_interval_km'
    ];

    protected function casts(): array
    {
        return [
            'last_service_date' => 'date',
            'fuel_consumption' => 'decimal:2',
            'service_interval_km' => 'integer'
        ];
    }

    // Default service interval
    const DEFAULT_SERVICE_INTERVAL = 5000;

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function usages(): HasMany
    {
        return $this->hasMany(VehicleUsage::class);
    }

    public function getLastUsageAttribute()
    {
        return $this->usages()->latest('end_km')->first();
    }

    public function getCurrentKmAttribute()
    {
        return $this->last_usage ? $this->last_usage->end_km : 0;
    }

    // Get KM saat service terakhir
    public function getLastServiceKmAttribute()
    {
        $lastService = $this->usages()
            ->where('usage_type', 'service')
            ->latest('service_date')
            ->first();

        return $lastService ? $lastService->end_km : 0;
    }

    // Get berapa KM sudah berjalan sejak service terakhir
    public function getKmSinceLastServiceAttribute()
    {
        return $this->current_km - $this->last_service_km;
    }

    // Get berapa KM lagi sampai service berikutnya
    public function getKmUntilNextServiceAttribute()
    {
        $interval = $this->service_interval_km ?? self::DEFAULT_SERVICE_INTERVAL;
        $nextServiceKm = $this->last_service_km + $interval;
        return $nextServiceKm - $this->current_km;
    }

    // Get KM untuk service berikutnya
    public function getNextServiceKmAttribute()
    {
        $interval = $this->service_interval_km ?? self::DEFAULT_SERVICE_INTERVAL;
        return $this->last_service_km + $interval;
    }

    // Get service terakhir
    public function getLastServiceAttribute()
    {
        return $this->usages()
            ->where('usage_type', 'service')
            ->latest('service_date')
            ->first();
    }

    // Check apakah sudah waktunya service
    public function needsService()
    {
        // Check berdasarkan KM (prioritas)
        if ($this->km_until_next_service <= 0) {
            return true;
        }

        // Check berdasarkan tanggal jika ada
        $lastService = $this->last_service;
        if ($lastService && $lastService->next_service_date && $lastService->next_service_date <= now()) {
            return true;
        }

        // Check berdasarkan service_schedule_days jika ada
        if ($this->service_schedule_days && $this->last_service_date) {
            $nextServiceDate = $this->last_service_date->addDays($this->service_schedule_days);
            if ($nextServiceDate <= now()) {
                return true;
            }
        }

        return false;
    }

    // Check apakah mendekati waktu service (default: 500km sebelum service)
    public function nearServiceThreshold($threshold = 500)
    {
        return $this->km_until_next_service > 0 && $this->km_until_next_service <= $threshold;
    }

    // Get status service
    public function getServiceStatusAttribute()
    {
        if ($this->needsService()) {
            return 'overdue';
        } elseif ($this->nearServiceThreshold()) {
            return 'due_soon';
        }
        return 'ok';
    }

    // Get warna untuk status service
    public function getServiceStatusColorAttribute()
    {
        return match ($this->service_status) {
            'overdue' => 'danger',
            'due_soon' => 'warning',
            default => 'success'
        };
    }

    // Get text untuk status service
    public function getServiceStatusTextAttribute()
    {
        if ($this->needsService()) {
            $overdue = abs($this->km_until_next_service);
            return "Overdue by {$overdue} km";
        } elseif ($this->nearServiceThreshold()) {
            return "Due in {$this->km_until_next_service} km";
        }
        return "Next service in {$this->km_until_next_service} km";
    }

    // Get progress percentage sampai service berikutnya
    public function getServiceProgressPercentageAttribute()
    {
        $interval = $this->service_interval_km ?? self::DEFAULT_SERVICE_INTERVAL;
        $kmSinceService = $this->km_since_last_service;

        if ($kmSinceService >= $interval) {
            return 100;
        }

        return round(($kmSinceService / $interval) * 100);
    }

    // Scope untuk vehicle yang perlu service
    public function scopeNeedingService($query)
    {
        return $query->get()->filter(function ($vehicle) {
            return $vehicle->needsService();
        });
    }

    // Scope untuk vehicle yang mendekati service
    public function scopeNearingService($query, $threshold = 500)
    {
        return $query->get()->filter(function ($vehicle) use ($threshold) {
            return $vehicle->nearServiceThreshold($threshold);
        });
    }
}