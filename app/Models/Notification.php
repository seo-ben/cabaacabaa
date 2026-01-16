<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id_notification';
    public $timestamps = false;
    protected $fillable = ['id_utilisateur','type_notification','titre','message','id_commande','id_vendeur','lue','date_lecture','date_creation'];

    protected $casts = [
        'date_lecture' => 'datetime',
        'date_creation' => 'datetime',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'id_utilisateur', 'id_user');
    }

    public function commande()
    {
        return $this->belongsTo(Commande::class, 'id_commande', 'id_commande');
    }

    public function vendeur()
    {
        return $this->belongsTo(Vendeur::class, 'id_vendeur', 'id_vendeur');
    }
}
