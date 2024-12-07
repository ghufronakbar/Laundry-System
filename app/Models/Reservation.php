<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Reservation extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika tidak mengikuti konvensi
    protected $table = 'reservations';

    // Tentukan apakah menggunakan UUID sebagai primary key
    public $incrementing = false;  // Nonaktifkan auto increment
    protected $keyType = 'string'; // Gunakan string (UUID) sebagai primary key

    // Tentukan kolom yang dapat diisi (fillable)
    protected $fillable = [
        'user_id',
        'machine_id',
        'machine_number',
        'status',
        'reservation_date',
        'reservation_end',
    ];

    protected $dates = ['reservation_date', 'reservation_end'];


    // Set UUID secara otomatis ketika membuat entri baru
    protected static function booted()
    {
        static::creating(function ($reservation) {
            $reservation->id = (string) Str::uuid(); // Menghasilkan UUID saat entri dibuat
        });
    }

    // Relasi dengan User (user_id)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi dengan Machine (machine_id)
    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    // Relasi dengan Payment (1 to 1)
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
