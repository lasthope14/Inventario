<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'role',
        'receives_notifications',
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
            'last_login_at' => 'datetime',
            'receives_notifications' => 'boolean',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function updateLastLoginAt()
    {
        $this->last_login_at = Date::now();
        $this->save();
    }
    
    public function shouldReceiveNotification()
    {
        // Si el usuario tiene desactivadas las notificaciones, no recibe ninguna
        if (!$this->receives_notifications) {
            Log::info('Usuario tiene desactivadas las notificaciones', [
                'usuario_id' => $this->id,
                'usuario_nombre' => $this->name
            ]);
            return false;
        }
        
        $roleName = $this->role->name ?? 'sin rol';
        $shouldReceive = in_array($roleName, ['administrador', 'almacenista']);
        
        Log::info('Verificando permisos de notificaci¨®n', [
            'usuario_id' => $this->id,
            'usuario_nombre' => $this->name,
            'rol' => $roleName,
            'debe_recibir' => $shouldReceive,
            'roles_permitidos' => ['administrador', 'almacenista']
        ]);

        return $shouldReceive;
    }
}