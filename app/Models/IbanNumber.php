<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IbanNumber extends Model
{
    use HasFactory;

    protected $fillable = [
        'iban_number',
        'user_id',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
