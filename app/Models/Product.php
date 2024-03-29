<?php

namespace App\Models;

use App\Http\Scopes\ProductScope;
use App\Utils\DateAttributeUtil;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory, ProductScope;

    const CUSTOM_CODE_PREFIX = 'CUSTOM-';

    protected $guarded = [
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function expirationDates(): HasMany
    {
        return $this->hasMany(ExpirationDate::class);
    }

    public function latestExpirationDate(): HasOne
    {
        return $this->hasOne(ExpirationDate::class)->latestOfMany('date');
    }

    protected function finishedAt(): Attribute
    {
        return DateAttributeUtil::dateAttribute();
    }

    protected function addedToPurchaseListAt(): Attribute
    {
        return DateAttributeUtil::dateAttribute();
    }
}
