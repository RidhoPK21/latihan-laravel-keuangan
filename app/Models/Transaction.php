<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';

    // Isi fillable untuk mass assignment
    protected $fillable = ['user_id', 'type', 'amount', 'date', 'description', 'cover'];

    // Casts untuk memastikan tipe data benar
    protected $casts = [
        'amount' => 'integer',
        'date' => 'date',
    ];
}




