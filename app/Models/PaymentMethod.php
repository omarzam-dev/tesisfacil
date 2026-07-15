<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = ['label', 'type', 'account_info', 'active', 'orden'];

    protected $casts = ['active' => 'boolean'];
}
