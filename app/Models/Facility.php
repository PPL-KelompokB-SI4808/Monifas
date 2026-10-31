<?php

namespace App\Models;

use App\Enums\FacilityCondition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facility extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'location_id',
        'building_id',
        'room_id',
        'facility_category_id',
        'code',
        'name',
        'specifications',
        'condition',
        'is_active',
    ];

    protected $casts = [
        'condition' => FacilityCondition::class,
        'is_active' => 'boolean',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function category()
    {
        return $this->belongsTo(FacilityCategory::class, 'facility_category_id');
    }
}