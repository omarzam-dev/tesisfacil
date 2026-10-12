<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = ['name', 'price', 'max_tesis', 'max_revisiones'];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
