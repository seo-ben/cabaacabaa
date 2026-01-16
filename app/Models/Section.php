<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $table = 'sections';
    protected $primaryKey = 'id';
    protected $fillable = ['id_vendeur','type_section','titre','contenu','position','active'];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function vendeur()
    {
        return $this->belongsTo(Vendeur::class, 'id_vendeur', 'id_vendeur');
    }
}
