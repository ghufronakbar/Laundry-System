<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory;
    protected $table = 'payments';

    // Tentukan apakah menggunakan UUID sebagai primary key
    public $incrementing = false;  // Nonaktifkan auto increment
    protected $keyType = 'string'; // Gunakan string (UUID) sebagai primary key

    protected $fillable = [
        'reservation_id',
        'total',
        'payment_method',
        'snap_token',
        'direct_url',
        'paid_at'
    ];

    // Relasi dengan Reservation (1 to 1)
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    protected static function booted()
    {
        static::creating(function ($machine) {
            $machine->id = (string) Str::uuid(); // Menghasilkan UUID saat entri dibuat
        });
    }
}
