<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sales_orders';

    protected $fillable = [
        'order_number',
        'cashier_id',
        'department_id',      // ✅ already here - good
        'customer_type',
        'table_number',
        'subtotal',
        'tax_amount',
        'total_amount',
        'amount_paid',
        'change_amount',
        'payment_method',
        'status',
        'payment_status',     // ✅ was MISSING - now added
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'subtotal'     => 'decimal:2',
        'tax_amount'   => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid'  => 'decimal:2',
        'change_amount'=> 'decimal:2',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'deleted_at'   => 'datetime',
    ];

    const STATUS_PENDING   = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    const CUSTOMER_DINE_IN  = 'dine_in';
    const CUSTOMER_TAKEAWAY = 'takeaway';
    const CUSTOMER_DELIVERY = 'delivery';

    const PAYMENT_UNPAID = 'unpaid';
    const PAYMENT_PAID = 'paid';

    const PAYMENT_CASH         = 'cash';
    const PAYMENT_CARD         = 'card';
    const PAYMENT_MOBILE_MONEY = 'mobile_money';

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function items()
    {
        return $this->hasMany(SalesOrderItem::class, 'sales_order_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('created_at', $date);
    }

    public function getFormattedTotalAttribute()
    {
        return 'UGX ' . number_format($this->total_amount, 2);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_PENDING   => '<span class="badge-pending">Pending</span>',
            self::STATUS_COMPLETED => '<span class="badge-approved">Completed</span>',
            self::STATUS_CANCELLED => '<span class="badge-rejected">Cancelled</span>',
            self::PAYMENT_UNPAID        => '<span class="badge-unpaid">Unpaid</span>',
            self::PAYMENT_PAID         => '<span class="badge-paid">Paid</span>',
        ];
        return $badges[$this->status] ?? '<span class="badge-pending">' . $this->status . '</span>';
    }
}
