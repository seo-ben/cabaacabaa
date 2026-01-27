<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $primaryKey = 'id_user';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'nom_complet',
        'telephone',
        'email',
        'password',
        'role',
        'statut_compte',
        'status',
        'photo_profil',
        'langue_preferee',
        'date_creation',
        'date_derniere_connexion',
        'derniere_ip',
        'last_login_ip',
        'suspended_at',
        'suspension_reason',
        'login_attempts',
        'locked_until',
        'is_verified',
        'email_verified_at',
        'risk_score',
        'suspicious_flags',
        'last_suspicious_activity',
        'failed_logins',
        'referral_code',
        'referred_by',
        'referral_balance',
        'latitude',
        'longitude',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'date_creation' => 'datetime',
        'date_derniere_connexion' => 'datetime',
        'suspended_at' => 'datetime',
        'locked_until' => 'datetime',
        'email_verified_at' => 'datetime',
        'last_suspicious_activity' => 'datetime',
        'suspicious_flags' => 'array',
        'failed_logins' => 'array',
        'is_verified' => 'boolean',
    ];

    // Relations
    public function vendeur()
    {
        return $this->hasOne(Vendeur::class, 'id_user', 'id_user');
    }

    public function commandes()
    {
        return $this->hasMany(Commande::class, 'id_client', 'id_user');
    }

    public function avis()
    {
        return $this->hasMany(AvisEvaluation::class, 'id_client', 'id_user');
    }

    public function favoris()
    {
        return $this->hasMany(FavorisClient::class, 'id_client', 'id_user');
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by', 'id_user');
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by', 'id_user');
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'coupon_user', 'id_user', 'id_coupon')->withPivot('used_at');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'id_utilisateur', 'id_user');
    }

    public function livraisons()
    {
        return $this->hasMany(Commande::class, 'id_livreur', 'id_user');
    }

    public function deliveryApplications()
    {
        return $this->hasMany(DeliveryApplication::class, 'id_user', 'id_user');
    }

    public function loginAttempts()
    {
        return $this->hasMany(LoginAttempt::class, 'id_user', 'id_user');
    }

    /**
     * Get unread notifications.
     */
    public function unreadNotifications()
    {
        return $this->notifications()->where('lue', false)->orderBy('date_creation', 'desc')->get();
    }

    /**
     * Determine if the user is a vendor/shop owner.
     * Checks common role values and a possible `has_shop` flag.
     */
    public function isVendor()
    {
        return ($this->role === 'vendor' || $this->role === 'vendeur') || (($this->has_shop ?? false) === true);
    }

    /**
     * Check if user is a super admin
     */
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user can apply to become a vendor
     */
    public function canApplyAsVendor()
    {
        // Don't allow if already a vendor
        if ($this->isVendor()) {
            return false;
        }

        // Don't allow if there is an existing vendor profile (pending or otherwise)
        if ($this->vendeur()->exists()) {
            return false;
        }

        return true;
    }

    /**
     * Check if user is an active delivery driver
     * A user is a driver if they have accepted delivery applications or assigned deliveries
     */
    public function isDriver()
    {
        // Check if user has any accepted delivery applications
        $hasAcceptedApplication = $this->deliveryApplications()
            ->where('status', 'accepted')
            ->exists();

        // Check if user has any deliveries assigned to them
        $hasAssignedDeliveries = $this->livraisons()->exists();

        return $hasAcceptedApplication || $hasAssignedDeliveries;
    }

    // ===== PERMISSIONS =====

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_user', 'user_id', 'permission_id');
    }

    public function hasPermission($key)
    {
        // Super Admin has all permissions
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->permissions->contains('key', $key);
    }

    // ===== SCOPES POUR FILTRAGE =====

    /**
     * Filtrer par rôle
     */
    public function scopeByRole($query, $role)
    {
        if ($role && $role !== 'tous') {
            return $query->where('role', $role);
        }
        return $query;
    }

    /**
     * Filtrer par statut
     */
    public function scopeByStatus($query, $status)
    {
        if (!$status || $status === 'tous') {
            return $query;
        }

        return match ($status) {
            'actif' => $query->where('status', 'actif')->whereNull('deleted_at'),
            'inactif' => $query->where('status', 'inactif')->whereNull('deleted_at'),
            'suspendu' => $query->where('status', 'suspendu')->whereNull('deleted_at'),
            'supprime' => $query->onlyTrashed(),
            default => $query
        };
    }

    /**
     * Filtrer par vérification
     */
    public function scopeVerified($query, $verified = true)
    {
        return $verified ? $query->where('is_verified', true) : $query->where('is_verified', false);
    }

    /**
     * Filtrer les comptes suspects (risk_score > 5)
     */
    public function scopeSuspicious($query, $isSuspicious = true)
    {
        return $isSuspicious
            ? $query->where('risk_score', '>', 5)
            : $query->where('risk_score', '<=', 5);
    }

    /**
     * Filtrer les comptes verrouillés
     */
    public function scopeLocked($query, $isLocked = true)
    {
        return $isLocked
            ? $query->whereNotNull('locked_until')->where('locked_until', '>', now())
            : $query->whereNull('locked_until')->orWhere('locked_until', '<=', now());
    }

    /**
     * Filtrer par recherche (nom, email, téléphone)
     */
    public function scopeSearch($query, $search)
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('nom_complet', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('telephone', 'like', "%$search%");
        });
    }

    // ===== MÉTHODES DE GESTION =====

    /**
     * Suspendre un utilisateur
     */
    public function suspend($reason = null)
    {
        $this->update([
            'status' => 'suspendu',
            'suspended_at' => now(),
            'suspension_reason' => $reason,
        ]);
    }

    /**
     * Lever la suspension
     */
    public function unsuspend()
    {
        $this->update([
            'status' => 'actif',
            'suspended_at' => null,
            'suspension_reason' => null,
        ]);
    }

    /**
     * Verrouiller le compte (trop de tentatives)
     */
    public function lock($minutes = 30)
    {
        $this->update([
            'locked_until' => now()->addMinutes($minutes),
            'login_attempts' => 0,
        ]);
    }

    /**
     * Déverrouiller le compte
     */
    public function unlock()
    {
        $this->update([
            'locked_until' => null,
            'login_attempts' => 0,
        ]);
    }

    /**
     * Ajouter un drapeau suspect
     */
    public function addSuspiciousFlag($flag)
    {
        $flags = $this->suspicious_flags ?? [];

        if (!in_array($flag, $flags)) {
            $flags[] = $flag;
            $this->update([
                'suspicious_flags' => $flags,
                'risk_score' => min($this->risk_score + 2, 100),
                'last_suspicious_activity' => now(),
            ]);
        }
    }

    /**
     * Augmenter le risk_score
     */
    public function increaseRiskScore($amount = 1)
    {
        $this->update([
            'risk_score' => min($this->risk_score + $amount, 100),
            'last_suspicious_activity' => now(),
        ]);
    }

    /**
     * Réinitialiser le risk_score
     */
    public function resetRiskScore()
    {
        $this->update([
            'risk_score' => 0,
            'suspicious_flags' => null,
            'login_attempts' => 0,
        ]);
    }

    /**
     * Vérifier si le compte est actif
     */
    public function isActive()
    {
        return $this->status === 'actif' && $this->deleted_at === null;
    }

    /**
     * Vérifier si le compte est suspendu
     */
    public function isSuspended()
    {
        return $this->status === 'suspendu' && $this->suspended_at !== null;
    }

    /**
     * Vérifier si le compte est verrouillé
     */
    public function isLocked()
    {
        return $this->locked_until && $this->locked_until > now();
    }

    /**
     * Vérifier si le compte est suspect
     */
    public function isSuspicious()
    {
        return $this->risk_score > 5;
    }

    /**
     * Vérifier si l'utilisateur est en ligne (dernière activité < 5 minutes)
     */
    public function isOnline()
    {
        return $this->date_derniere_connexion && $this->date_derniere_connexion->gt(now()->subMinutes(5));
    }

    /**
     * Vérifier si le compte est vérifié
     */
    public function isVerified()
    {
        return $this->is_verified === true;
    }
}
