<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = 'tags';
    protected $primaryKey = 'id_tag';
    protected $fillable = ['nom','slug'];

    public function plats()
    {
        return $this->belongsToMany(Plat::class, 'plat_tag', 'id_tag', 'id_plat');
    }
}
