<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tanggal_lahir', // Tambahkan tanggal lahir
        'referal_id',    // Tambahkan referal_id
        'nomor_hp',      // Tambahkan nomor HP
        'alamat',        // Tambahkan alamat
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'tanggal_lahir' => 'date', // Cast tanggal lahir ke date
    ];

    /**
     * Relasi self-referencing untuk referal.
     * 
     * Banyak user bisa direfer oleh satu user (Many-to-One).
     */
    public function referer()
    {
        return $this->belongsTo(User::class, 'referal_id'); // User yang mereferensikan
    }

    /**
     * Relasi one-to-many untuk user yang direferensikan.
     * 
     * Satu user bisa mereferensikan banyak user (One-to-Many).
     */
    public function referredUsers()
    {
        return $this->hasMany(User::class, 'referal_id'); // User yang direferensikan
    }

    /**
     * Relasi ke tabel profil (contoh relasi one-to-one).
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
}
