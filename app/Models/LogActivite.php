<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogActivite extends Model
{
    protected $table = 'logs_activite';
    protected $primaryKey = 'id_log';
    public $timestamps = false;
    protected $fillable = ['id_utilisateur','type_action','table_cible','id_enregistrement','details_action','adresse_ip','user_agent','date_action'];

    protected $casts = [
        'details_action' => 'array',
        'date_action' => 'datetime',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'id_utilisateur', 'id_user');
    }
}
