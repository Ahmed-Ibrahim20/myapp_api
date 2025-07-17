<?php

    namespace App\Models;

    use Illuminate\Contracts\Auth\MustVerifyEmail;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Illuminate\Notifications\Notifiable;
    use Laravel\Sanctum\HasApiTokens;


    class User extends Authenticatable
    {
        use HasApiTokens, HasFactory, Notifiable;
        /**
         * The attributes that are mass assignable.
         */
        protected $fillable = [
            'name',
            'email',
            'phone',
            'password',
            'address',
            'role',
            'user_add_id'
        ];

        /**
         * The attributes that should be hidden for arrays (e.g., JSON responses).
         */
        protected $hidden = [
            'password',
            'remember_token',
        ];

        /**
         * The attributes that should be cast to native types.
         */
        protected $casts = [
            'email_verified_at' => 'datetime',
            'role' => 'integer',
        ];

        /**
         * Get a human-readable role (optional helper).
         */
        public function getRoleNameAttribute()
        {
            return match ($this->role) {
                0 => 'Customer',
                1 => 'Farmer',
                2 => 'Admin',
                default => 'Unknown',
            };
        }
    }