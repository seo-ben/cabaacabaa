<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavorisClient extends Model
{
    protected $table = 'favoris_clients';
    protected $primaryKey = 'id_favori';
    public $timestamps = false;
    protected $fillable = ['id_client','id_vendeur','date_ajout'];

    protected $casts = [
        'date_ajout' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'id_client', 'id_user');
    }

    public function vendeur()
    {
        return $this->belongsTo(Vendeur::class, 'id_vendeur', 'id_vendeur');
    }
}
