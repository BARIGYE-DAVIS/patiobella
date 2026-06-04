<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vendor_code',
        'name',
        'contact_person',
        'email',
        'phone',
        'alternative_phone',
        'address',
        'city',
        'country',
        'tax_id',
        'payment_method',
        'credit_limit',
        'status',
        'average_rating',
        'total_ratings',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'credit_limit' => 'integer',
            'average_rating' => 'decimal:1',
            'total_ratings' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([$this->address, $this->city, $this->country]);
        return implode(', ', $parts);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByPaymentMethod($query, string $method)
    {
        return $query->where('payment_method', $method);
    }

    // =====================================================
    // Relationships
    // =====================================================

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'vendor_id');
    }

    public function goodsReceivedNotes()
    {
        return $this->hasMany(GoodsReceivedNote::class, 'vendor_id');
    }

    public function grns()
    {
        return $this->hasMany(GoodsReceivedNote::class, 'vendor_id');
    }

    public function ratings()
    {
        return $this->hasMany(VendorRating::class, 'vendor_id');
    }

    // =====================================================
    // NEW: Categories Relationship (Many-to-Many)
    // =====================================================

    /**
     * Categories that this vendor supplies
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'vendor_categories')
                    ->withTimestamps();
    }

    /**
     * Get category IDs supplied by this vendor
     */
    public function getCategoryIdsAttribute()
    {
        return $this->categories->pluck('id')->toArray();
    }

    /**
     * Check if vendor supplies a specific category
     */
    public function suppliesCategory($categoryId)
    {
        return $this->categories()->where('category_id', $categoryId)->exists();
    }

    /**
     * Sync categories supplied by this vendor
     */
    public function syncCategories($categoryIds)
    {
        $this->categories()->sync($categoryIds);
    }

    // =====================================================
    // Rating Methods
    // =====================================================

    public function updateAverageRating()
    {
        $avg = $this->ratings()->avg('rating');
        $count = $this->ratings()->count();

        $this->average_rating = $avg ? round($avg, 1) : 0;
        $this->total_ratings = $count;
        $this->save();

        return $this;
    }

    public function hasRatings()
    {
        return $this->total_ratings > 0;
    }

    public function getStarDisplayAttribute()
    {
        $rating = $this->average_rating;

        if ($rating == 0) {
            return '<span class="text-xs text-gray-400">No ratings yet</span>';
        }

        $fullStars = floor($rating);
        $halfStar = ($rating - $fullStars) >= 0.5;
        $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

        $html = '';
        for ($i = 0; $i < $fullStars; $i++) {
            $html .= '<i class="fas fa-star text-yellow-400"></i>';
        }
        if ($halfStar) {
            $html .= '<i class="fas fa-star-half-alt text-yellow-400"></i>';
        }
        for ($i = 0; $i < $emptyStars; $i++) {
            $html .= '<i class="far fa-star text-yellow-400"></i>';
        }

        if ($this->total_ratings > 0) {
            $html .= '<span class="text-xs text-gray-500 ml-1">(' . $this->total_ratings . ')</span>';
        }

        return $html;
    }

    public function getAverageRatingAttribute($value)
    {
        return $value ? (float) $value : 0;
    }
}
