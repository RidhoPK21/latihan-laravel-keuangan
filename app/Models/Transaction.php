<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    /**
     * Bidang (field) yang boleh diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
       'user_id',
       'title',
       'description',
       'amount',
       'type',
       'cover', 
       'date', // <-- TAMBAHKAN BARIS INI
   ];
   protected $casts = [
        'date' => 'datetime',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}