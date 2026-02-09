<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ServiceType;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'salutation',
        'name',
        'service_type_id',
        'area',
        'charges',
        'mobile',
        'status',
        'slug',
        'vehicle_number', // ✅ ADD THIS
    ];

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }
}
