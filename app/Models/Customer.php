<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
    ];

    /**
     * Get all of the favorite products for the customer.
     */
    public function favorites()
    {
        return $this->hasMany(CustomerFavorite::class);
    }
}
