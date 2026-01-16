<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $primaryKey = 'id_coupon';
    protected $fillable = [
        'id_vendeur',
        'code',
        'type',
        'valeur',
        'montant_minimal_achat',
        'limite_utilisation',
        'nombre_utilisations',
        'expire_at',
        'actif'
    ];

    public function vendeur()
    {
        return $this->belongsTo(Vendeur::class, 'id_vendeur', 'id_vendeur');
    }

    protected $casts = [
        'expire_at' => 'datetime',
        'actif' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'coupon_user', 'id_coupon', 'id_user')->withPivot('used_at');
    }

    public function isValid()
    {
        if (!$this->actif)
            return false;
        if ($this->expire_at && $this->expire_at->isPast())
            return false;
        if ($this->limite_utilisation && $this->nombre_utilisations >= $this->limite_utilisation)
            return false;
        return true;
    }
}
