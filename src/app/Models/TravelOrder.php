<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelOrder extends Model
{
    /** @use HasFactory<\Database\Factories\TravelOrderFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'requester_name',
        'destination',
        'start_date',
        'end_date',
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $travelOrder = $this->where($field ?? $this->getRouteKeyName(), $value)->first();

        if (!$travelOrder) {
            abort(response()->json([
                'message' => 'Nenhuma ordem de viagem encontrada.',
            ], 404));
        }

        return $travelOrder;
    }

    public function scopeStatusFilter($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    public function scopeDestinationFilter($query, $destination)
    {
        if ($destination) {
            return $query->where('destination', 'like', "%$destination%");
        }
        return $query;
    }

    public function scopeDateRangeFilter($query, $startDate, $endDate)
    {
        if ($startDate && $endDate) {
            return $query->whereBetween('start_date', [$startDate, $endDate])
                ->orWhereBetween('end_date', [$startDate, $endDate]);
        }
        return $query;
    }
}
