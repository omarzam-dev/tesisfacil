<?php
namespace App\Http\Controllers;

use App\Models\PaymentProof;
use App\Models\PaymentMethod;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    private function checkAdmin()
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'No tienes permiso para acceder aquí.');
        }
    }

    public function index()
    {
        $this->checkAdmin();

        $pendingPayments = PaymentProof::where('status', 'pending')
            ->with(['user', 'plan'])
            ->latest()
            ->get();

        return view('admin.index', compact('pendingPayments'));
    }

    public function payments()
    {
        $this->checkAdmin();

        $proofs = PaymentProof::with(['user', 'plan'])->latest()->get();

        return view('admin.payments', compact('proofs'));
    }

    public function approve(PaymentProof $proof)
    {
        $this->checkAdmin();

        $proof->update(['status' => 'approved']);

        // Si el usuario ya tenía una suscripción activa (renovación), la marca
        // como vencida antes de crear la nueva, para no dejar dos "activas" a la vez.
        Subscription::where('user_id', $proof->user_id)
            ->where('status', 'active')
            ->update(['status' => 'expired']);

        Subscription::create([
            'user_id'   => $proof->user_id,
            'plan_id'   => $proof->plan_id,
            'status'    => 'active',
            'starts_at' => now(),
            'ends_at'   => now()->addDays(30),
        ]);

        $proof->user->update([
            'subscription_status' => 'active',
            'recordatorio_enviado_at' => null,
        ]);

        return back()->with('success', 'Suscripción activada.');
    }

    public function reject(PaymentProof $proof)
    {
        $this->checkAdmin();

        $proof->update(['status' => 'rejected']);
        $proof->user->update(['subscription_status' => 'expired']);

        return back()->with('success', 'Comprobante rechazado.');
    }

    public function plans()
    {
        $this->checkAdmin();

        $plans = Plan::orderBy('price')->get();

        return view('admin.plans', compact('plans'));
    }

    public function updatePlan(Request $request, Plan $plan)
    {
        $this->checkAdmin();

        $data = $request->validate([
            'price'          => 'required|numeric|min:0',
            'max_tesis'      => 'required|integer|min:1',
            'max_revisiones' => 'required|integer|min:1',
        ]);

        $plan->update($data);

        return back()->with('success', "Plan \"{$plan->name}\" actualizado correctamente.");
    }

    public function subscriptions()
    {
        $this->checkAdmin();

        $subscriptions = Subscription::with(['user', 'plan'])->latest()->get();

        return view('admin.subscriptions', compact('subscriptions'));
    }

    public function cancelSubscription(Subscription $subscription)
    {
        $this->checkAdmin();

        $subscription->update(['status' => 'cancelled']);

        // Solo baja el acceso del usuario si esta era su suscripción vigente
        if ($subscription->user->subscription_status === 'active') {
            $subscription->user->update(['subscription_status' => 'expired']);
        }

        return back()->with('success', 'Suscripción cancelada.');
    }

    public function destroySubscription(Subscription $subscription)
    {
        $this->checkAdmin();

        if ($subscription->status === 'active') {
            return back()->with('error', 'No puedes eliminar una suscripción activa. Primero cancélala.');
        }

        $subscription->delete();

        return back()->with('success', 'Suscripción eliminada.');
    }

    public function paymentMethods()
    {
        $this->checkAdmin();

        $methods = PaymentMethod::orderBy('orden')->get();

        return view('admin.payment-methods', compact('methods'));
    }

    public function storePaymentMethod(Request $request)
    {
        $this->checkAdmin();

        $data = $request->validate([
            'label' => 'required|string|max:100',
            'type' => 'required|string|max:50',
            'account_info' => 'required|string|max:100',
        ]);

        PaymentMethod::create($data + ['active' => true]);

        return back()->with('success', 'Cuenta de pago agregada.');
    }

    public function updatePaymentMethod(Request $request, PaymentMethod $method)
    {
        $this->checkAdmin();

        $data = $request->validate([
            'label' => 'required|string|max:100',
            'type' => 'required|string|max:50',
            'account_info' => 'required|string|max:100',
        ]);

        $data['active'] = $request->boolean('active');

        $method->update($data);

        return back()->with('success', 'Cuenta de pago actualizada.');
    }

    public function users()
    {
        $this->checkAdmin();

        $users = User::withCount('tesis')->latest()->get();

        return view('admin.users', compact('users'));
    }

    public function toggleAdmin(User $user)
    {
        $this->checkAdmin();

        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes quitarte el rol de admin a ti mismo.');
        }

        $user->update(['role' => $user->isAdmin() ? 'user' : 'admin']);

        return back()->with('success', "{$user->name} ahora es " . ($user->isAdmin() ? 'administrador' : 'usuario normal') . '.');
    }
}
