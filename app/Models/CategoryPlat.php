<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryPlat extends Model
{
    protected $table = 'categories_plats';
    protected $primaryKey = 'id_categorie';
    public $timestamps = false;
    protected $fillable = ['nom_categorie','description','icone','ordre_affichage','actif'];

    public function plats()
    {
        return $this->hasMany(Plat::class, 'id_categorie', 'id_categorie');
    }
}
