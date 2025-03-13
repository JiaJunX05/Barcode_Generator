<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Barcode;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'image',
        'sku_code',
    ];

    public function barcode(): HasOne {
        return $this->hasOne(Barcode::class);
    }
}
