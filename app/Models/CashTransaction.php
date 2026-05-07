<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_register_id',
        'invoice_id',
        'payment_id',
        'type',
        'method',
        'amount',
        'reference',
        'note',
    ];

    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
