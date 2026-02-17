<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'price',
        'dimensions',
        'material',
        'prodCode',
        'description',
        'active',
        'addeb_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'active' => 'boolean',
        'addeb_by' => 'integer',
    ];

    public function photos(): HasMany
    {
        return $this->hasMany(ProductPhoto::class, 'product_id');
    }
}