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
        'code',
        'name',
        'location_id',
        'building_id',
        'room_id',
        'category_id',
        'condition',
        'description',
    ];

    protected $casts = [
        'condition' => FacilityCondition::class,
    ];

    public function location()
    {
        return $this->belongsTo(\App\Models\Location::class);
    }

    public function building()
    {
        return $this->belongsTo(\App\Models\Building::class);
    }

    public function room()
    {
        return $this->belongsTo(\App\Models\Room::class);
    }

    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class);
    }
}