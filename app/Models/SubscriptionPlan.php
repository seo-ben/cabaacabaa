<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'price',
        'product_limit',
        'staff_limit',
        'coupon_limit',
        'has_gallery',
        'has_socials',
        'is_boosted',
        'can_recruit_drivers',
        'is_active'
    ];

    protected $casts = [
        'has_gallery' => 'boolean',
        'has_socials' => 'boolean',
        'is_boosted' => 'boolean',
        'can_recruit_drivers' => 'boolean',
        'is_active' => 'boolean',
        'price' => 'integer',
        'product_limit' => 'integer',
        'staff_limit' => 'integer',
        'coupon_limit' => 'integer',
    ];

    public function vendeurs()
    {
        return $this->hasMany(Vendeur::class, 'subscription_plan_id');
    }
}
