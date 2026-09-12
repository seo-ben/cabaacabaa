<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Vendeur extends Model
{
    protected $table = 'vendeurs';
    protected $primaryKey = 'id_vendeur';
    public $timestamps = false;
    protected $fillable = [
        'id_user',
        'id_zone',
        'nom_commercial',
        'slug',
        'description',
        'type_vendeur',
        'adresse_complete',
        'latitude',
        'longitude',
        'horaires_ouverture',
        'telephone_commercial',
        'registre_commerce',
        'document_identite',
        'justificatif_domicile',
        'statut_verification',
        'date_verification',
        'note_moyenne',
        'nombre_avis',
        'nombre_commandes_total',
        'nombre_commandes_mois',
        'image_principale',
        'images_galerie',
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'tiktok_url',
        'whatsapp_number',
        'website_url',
        'wallet_balance',
        'id_category_vendeur',
        'is_boosted',
        'boost_expires_at',
        'subscription_plan_id',
        'trial_ends_at',
        'subscription_expires_at',
        'trial_used',
        'actif',
        'is_busy',
        'delivery_rate_per_km'
    ];

    protected $casts = [
        'horaires_ouverture' => 'array',
        'images_galerie' => 'array',
        'date_inscription' => 'datetime',
        'date_verification' => 'datetime',
        'boost_expires_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'subscription_expires_at' => 'datetime',
        'trial_used' => 'boolean',
    ];

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    /**
     * Get the limit for products (plats) based on the plan.
     */
    public function getPlatLimit()
    {
        return $this->plan ? $this->plan->product_limit : 5;
    }

    /**
     * Get the limit for staff members based on the plan.
     */
    public function getStaffLimit()
    {
        return $this->plan ? $this->plan->staff_limit : 1;
    }

    /**
     * Get the limit for active coupons based on the plan.
     */
    public function getCouponLimit()
    {
        return $this->plan ? $this->plan->coupon_limit : 0;
    }

    /**
     * Check if the vendor can use galleries.
     */
    public function canUseGallery()
    {
        return $this->plan ? $this->plan->has_gallery : false;
    }

    /**
     * Check if the vendor can use social links.
     */
    public function canUseSocials()
    {
        return $this->plan ? $this->plan->has_socials : false;
    }

    /**
     * Check if the vendor can recruit drivers.
     */
    public function canRecruitDrivers()
    {
        return $this->plan ? $this->plan->can_recruit_drivers : false;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($vendeur) {
            if (empty($vendeur->slug)) {
                $vendeur->slug = Str::slug($vendeur->nom_commercial);
            }
            
            // Logic for first registration (Trial + Boost)
            if (!$vendeur->trial_used) {
                $vendeur->is_boosted = true;
                $vendeur->boost_expires_at = now()->addDays(15);
                $vendeur->trial_ends_at = now()->addMonth();
                $vendeur->trial_used = true; // Mark that trial is assigned
                
                // Assign Free Plan ID (price = 0)
                $freePlan = \App\Models\SubscriptionPlan::where('price', 0)->first();
                if ($freePlan) {
                    $vendeur->subscription_plan_id = $freePlan->id;
                }
            }
        });

        static::updating(function ($vendeur) {
            if ($vendeur->isDirty('nom_commercial') && empty($vendeur->slug)) {
                $vendeur->slug = Str::slug($vendeur->nom_commercial);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function zone()
    {
        return $this->belongsTo(ZoneGeographique::class, 'id_zone', 'id_zone');
    }

    public function plats()
    {
        return $this->hasMany(Plat::class, 'id_vendeur', 'id_vendeur');
    }

    public function commandes()
    {
        return $this->hasMany(Commande::class, 'id_vendeur', 'id_vendeur');
    }

    public function contacts()
    {
        return $this->hasMany(VendeurContact::class, 'id_vendeur', 'id_vendeur');
    }

    public function horaires()
    {
        return $this->hasMany(VendeurHoraire::class, 'id_vendeur', 'id_vendeur');
    }

    public function sections()
    {
        return $this->hasMany(Section::class, 'id_vendeur', 'id_vendeur');
    }

    public function medias()
    {
        return $this->hasMany(Media::class, 'id_vendeur', 'id_vendeur');
    }

    public function avisEvaluations()
    {
        return $this->hasMany(AvisEvaluation::class, 'id_vendeur', 'id_vendeur');
    }

    public function categories()
    {
        return $this->belongsToMany(CategoryPlat::class, 'vendeur_categories', 'id_vendeur', 'id_categorie');
    }

    public function payoutRequests()
    {
        return $this->hasMany(PayoutRequest::class, 'id_vendeur', 'id_vendeur');
    }

    public function category()
    {
        return $this->belongsTo(VendorCategory::class, 'id_category_vendeur', 'id_category_vendeur');
    }

    public function coupons()
    {
        return $this->hasMany(Coupon::class, 'id_vendeur', 'id_vendeur');
    }

    public function staff()
    {
        return $this->hasMany(VendorStaff::class, 'id_vendeur', 'id_vendeur');
    }

    public function deliveryRequests()
    {
        return $this->hasMany(DeliveryRequest::class, 'id_vendeur', 'id_vendeur');
    }

    /**
     * Obtenir l'URL de l'image principale ou l'image par défaut.
     */
    public function getImageUrlAttribute()
    {
        if ($this->image_principale && file_exists(public_path('storage/' . $this->image_principale))) {
            return asset('storage/' . $this->image_principale);
        }
        return 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=800&auto=format&fit=crop';
    }

    /**
     * Obtenir l'URL de la miniature ou l'image par défaut.
     */
    public function getThumbnailUrlAttribute()
    {
        if ($this->image_principale) {
            $thumbPath = dirname($this->image_principale) . '/thumbnails/' . basename($this->image_principale);
            if (file_exists(public_path('storage/' . $thumbPath))) {
                return asset('storage/' . $thumbPath);
            }
            if (file_exists(public_path('storage/' . $this->image_principale))) {
                return asset('storage/' . $this->image_principale);
            }
        }
        return 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=400&auto=format&fit=crop';
    }
}
