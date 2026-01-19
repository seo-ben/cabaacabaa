<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $primaryKey = 'id_country';
    protected $fillable = ['name', 'phone_prefix', 'flag_icon', 'is_active'];
}
