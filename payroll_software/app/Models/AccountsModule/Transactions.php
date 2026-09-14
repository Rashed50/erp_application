<?php

namespace App\Models\AccountsModule;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\AccountsModule\TransactionsDetail;

class Transactions extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'transactions';

    protected $fillable = [
        'TranDate',
        'TranAmount',
        'TranTypeId',
    ];

    protected $casts = [
        'TranDate' => 'date',
        'TranAmount' => 'float',
    ];

}
