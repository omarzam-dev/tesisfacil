<?php
namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        PaymentMethod::updateOrCreate(
            ['label' => 'Yape - Omar Zamudio'],
            ['type' => 'Yape', 'account_info' => '981 763 601', 'active' => true, 'orden' => 1]
        );

        PaymentMethod::updateOrCreate(
            ['label' => 'BCP - Cuenta corriente'],
            ['type' => 'BCP', 'account_info' => '000-000000000-0-00', 'active' => true, 'orden' => 2]
        );
    }
}
