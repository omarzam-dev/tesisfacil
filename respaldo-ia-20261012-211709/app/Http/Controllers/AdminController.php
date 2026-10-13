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

        // La duración sale del plan comprado (30 = mensual, 180 = semestral, 365 = anual)
        $dias = $proof->plan?->duration_days ?: 30;

        Subscription::create([
            'user_id'   => $proof->user_id,
            'plan_id'   => $proof->plan_id,
            'status'    => 'active',
            'starts_at' => now(),
            'ends_at'   => now()->addDays($dias),
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

        $plans = Plan::orderBy('orden')->orderBy('price')->get();

        // Gasto del asistente de IA en el mes actual
        $usosMes  = \App\Models\UsoIa::esteMes()->count();
        $gastoMes = (float) \App\Models\UsoIa::esteMes()->sum('costo_usd');

        return view('admin.plans', compact('plans', 'usosMes', 'gastoMes'));
    }

    public function storePlan(Request $request)
    {
        $this->checkAdmin();

        $data = $this->validarPlan($request);

        $plan = Plan::create($data);
        $this->dejarUnSoloDestacado($plan);

        return back()->with('success', "Plan \"{$plan->name}\" creado correctamente.");
    }

    public function updatePlan(Request $request, Plan $plan)
    {
        $this->checkAdmin();

        $data = $this->validarPlan($request);

        $plan->update($data);
        $this->dejarUnSoloDestacado($plan);

        return back()->with('success', "Plan \"{$plan->name}\" actualizado correctamente.");
    }

    /** Reglas comunes para crear y editar un plan. */
    private function validarPlan(Request $request): array
    {
        $data = $request->validate([
            'name'           => 'required|string|max:60',
            'price'          => 'required|numeric|min:0',
            'duration_days'  => 'required|integer|in:' . implode(',', array_keys(Plan::PERIODOS)),
            'max_tesis'      => 'required|integer|min:1',
            'max_revisiones' => 'required|integer|min:1',
            'max_ia'         => 'required|integer|min:0|max:9999',
            'orden'          => 'required|integer|min:0|max:999',
        ]);

        // Las casillas no se envían cuando están desmarcadas
        $data['destacado'] = $request->boolean('destacado');
        $data['activo']    = $request->boolean('activo');

        return $data;
    }

    /** "Recomendado" solo puede tenerlo un plan a la vez. */
    private function dejarUnSoloDestacado(Plan $plan): void
    {
        if ($plan->destacado) {
            Plan::where('id', '!=', $plan->id)->update(['destacado' => false]);
        }
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
