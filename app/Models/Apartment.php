<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Apartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'rooms',
        'beds',
        'bathrooms',
        'dimension_mq',
        'image',
        'latitude',
        'longitude',
        'address_full',
        'is_visible',
        //METTO IL TYPE ID PER COLLEGARLO ALLA TABELLA TYPE
        "service_id",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }
}
