<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Services extends Model
{
    use HasFactory;
     protected $fillable = [
        'prestataire_id',
        'title',
        'price',
     ];
     public function prestataire ()
     {
         return $this->belongsTo(Prestataire::class);
     }
}
