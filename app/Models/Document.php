<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'purchase_order_id',
        'grn_id',
        'document_type',
        'filename',
        'original_name',
        'path',
        'file_size',
        'mime_type',
        'uploaded_by',
        'updated_by'
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function goodsReceivedNote()
    {
        return $this->belongsTo(GoodsReceivedNote::class, 'grn_id');
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
