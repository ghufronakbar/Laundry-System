<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Admin extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika tidak mengikuti konvensi (Laravel akan menebak nama tabel secara otomatis, tetapi Anda dapat menentukannya di sini)
    protected $table = 'admins';

    // Tentukan apakah menggunakan UUID sebagai primary key
    public $incrementing = false;  // Nonaktifkan auto increment
    protected $keyType = 'string'; // Gunakan string (UUID) sebagai primary key

    // Tentukan kolom yang dapat diisi (fillable)
    protected $fillable = [
        'name', 'email', 'password'
    ];

    // Set UUID secara otomatis ketika membuat entri baru
    protected static function booted()
    {
        static::creating(function ($admin) {
            $admin->id = (string) Str::uuid(); // Menghasilkan UUID saat entri dibuat
        });
    }
}
