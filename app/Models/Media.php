<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table = 'medias';
    protected $primaryKey = 'id';
    protected $fillable = ['id_vendeur','id_plat','type','chemin','titre','description','ordre','meta'];

    protected $casts = [
        'meta' => 'array',
    ];

    public function vendeur()
    {
        return $this->belongsTo(Vendeur::class, 'id_vendeur', 'id_vendeur');
    }

    public function plat()
    {
        return $this->belongsTo(Plat::class, 'id_plat', 'id_plat');
    }
}
