<?php
namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\PaymentProof;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function expired()
    {
        return view('subscription.expired');
    }

    public function plans()
    {
        $plans = Plan::orderBy('price')->get();

        return view('subscription.plans', compact('plans'));
    }

    public function checkout(Plan $plan)
    {
        $paymentMethods = \App\Models\PaymentMethod::where('active', true)->orderBy('orden')->get();

        return view('subscription.checkout', compact('plan', 'paymentMethods'));
    }

    public function uploadProof(Request $request, Plan $plan)
    {
        $request->validate([
            'method'          => ['required', 'string', 'max:100'],
            'proof_image'     => ['required', 'image', 'max:4096'],
            'transaction_ref' => ['nullable', 'string', 'max:100'],
        ]);

        $path = $request->file('proof_image')->store('proofs', 'public');

        PaymentProof::create([
            'user_id'         => auth()->id(),
            'plan_id'         => $plan->id,
            'method'          => $request->method,
            'proof_image'     => $path,
            'transaction_ref' => $request->transaction_ref,
            'status'          => 'pending',
        ]);

        auth()->user()->update(['subscription_status' => 'pending']);

        return redirect()->route('subscription.pending');
    }

    public function pending()
    {
        return view('subscription.pending');
    }

    public function manage()
    {
        $user = auth()->user();
        $plans = Plan::orderBy('price')->get();
        $activeSubscription = $user->activeSubscription;
        $paymentProofs = $user->paymentProofs()->with('plan')->latest()->get();

        return view('subscription.manage', compact('user', 'plans', 'activeSubscription', 'paymentProofs'));
    }

    public function cancel()
    {
        $user = auth()->user();

        $user->activeSubscription?->update(['status' => 'cancelled']);
        $user->update(['subscription_status' => 'expired']);

        return redirect()->route('subscription.plans')
            ->with('success', 'Tu suscripción fue cancelada. Puedes elegir un nuevo plan cuando quieras.');
    }
}
