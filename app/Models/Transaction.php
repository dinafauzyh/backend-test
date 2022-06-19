<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function expedition()
    {
        return $this->belongsTo(Expedition::class);
    }

    public function transaction_details()
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
