<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'phone',
        'address',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Obtener solo pedidos pagados (confirmados)
     */
    public function paidOrders()
    {
        return $this->hasMany(Order::class)->where('payment_status', 'paid');
    }

    /**
     * Obtener solo pedidos completados
     */
    public function completedOrders()
    {
        return $this->hasMany(Order::class)->where('status', 'delivered');
    }

    /**
     * Calcular total gastado (solo pedidos pagados)
     */
    public function getTotalSpentAttribute()
    {
        return $this->paidOrders()->sum('total');
    }

    /**
     * Contar pedidos pagados
     */
    public function getPaidOrdersCountAttribute()
    {
        return $this->paidOrders()->count();
    }

    public function isAdmin()
    {
        return $this->is_admin === true;
    }
    public function sendEmailVerificationNotification()
{
    $this->notify(new \App\Notifications\VerifyEmailNotification());
}

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/avatars/' . $this->avatar);
        }

        $name = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name}&background=0A0A0A&color=FAFAF8&size=100&rounded=true";
    }
}
