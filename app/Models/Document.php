<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'purchase_order_id',
        'po_id',
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
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function po()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id');
    }

    public function goodsReceivedNote()
    {
        return $this->belongsTo(GoodsReceivedNote::class, 'grn_id');
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
