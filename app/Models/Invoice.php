<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'series',
        'number',
        'customer_id',
        'user_id',
        'customer_name',
        'customer_document',
        'subtotal',
        'discount',
        'tax_total',
        'total',
        'status',
        'issue_date',
        'due_date',
        'note',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
    ];

    protected $appends = [
        'paid_amount',
        'due_amount',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getPaidAmountAttribute()
    {
        return $this->payments()->sum('amount');
    }

    public function getDueAmountAttribute()
    {
        return max(0, ($this->total ?? 0) - $this->paid_amount);
    }

    public function updatePaymentStatus()
    {
        $paid = $this->paid_amount;
        $total = $this->total ?? 0;

        $status = 'due';
        if ($paid >= $total && $total > 0) {
            $status = 'paid';
        } elseif ($paid > 0) {
            $status = 'partial';
        }

        if ($this->status !== $status) {
            $this->update(['status' => $status]);
        }

        return $status;
    }
}
