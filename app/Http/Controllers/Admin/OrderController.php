<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * View all orders in the system.
     */
    public function index(Request $request)
    {
        $status = $request->get('status');
        $search = $request->get('search');
        $date_start = $request->get('date_start');
        $date_end = $request->get('date_end');

        $query = Commande::with(['client', 'vendeur', 'lignes']);

        // Sorting: Today's orders first, then by date descending
        $query->orderByRaw("DATE(date_commande) = CURDATE() DESC")
            ->orderBy('date_commande', 'DESC');

        // Filters
        if ($status) {
            $query->where('statut', $status);
        }

        if ($date_start) {
            $query->whereDate('date_commande', '>=', $date_start);
        }

        if ($date_end) {
            $query->whereDate('date_commande', '<=', $date_end);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('numero_commande', 'LIKE', "%{$search}%")
                    ->orWhereHas('client', function ($cq) use ($search) {
                        $cq->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('phone', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('vendeur', function ($vq) use ($search) {
                        $vq->where('nom_commercial', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Expanded stats for total monitoring (including financial sums)
        $statsData = Commande::query()
            ->selectRaw("
                COUNT(*) as total_count,
                SUM(CASE WHEN statut = 'en_attente' THEN 1 ELSE 0 END) as pending_count,
                SUM(CASE WHEN statut = 'en_attente' THEN montant_total ELSE 0 END) as pending_sum,
                SUM(CASE WHEN statut = 'termine' THEN 1 ELSE 0 END) as completed_count,
                SUM(CASE WHEN statut = 'termine' THEN montant_total ELSE 0 END) as completed_sum,
                SUM(CASE WHEN statut = 'en_preparation' THEN 1 ELSE 0 END) as prep_count,
                SUM(CASE WHEN statut = 'pret' THEN 1 ELSE 0 END) as ready_count,
                SUM(CASE WHEN statut = 'annule' THEN 1 ELSE 0 END) as cancelled_count,
                AVG(CASE WHEN statut = 'termine' THEN montant_total ELSE NULL END) as avg_order_value,
                SUM(frais_service) as total_fees,
                SUM(montant_total) as total_revenue
            ")
            ->first();

        $stats = [
            'total' => $statsData->total_count,
            'total_revenue' => $statsData->total_revenue ?? 0,
            'avg_order' => $statsData->avg_order_value ?? 0,
            'total_fees' => $statsData->total_fees ?? 0,
            'en_attente' => [
                'count' => $statsData->pending_count,
                'sum' => $statsData->pending_sum ?? 0
            ],
            'termine' => [
                'count' => $statsData->completed_count,
                'sum' => $statsData->completed_sum ?? 0
            ],
            'en_preparation' => $statsData->prep_count,
            'pret' => $statsData->ready_count,
            'annule' => $statsData->cancelled_count,
        ];

        $orders = $query->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders', 'status', 'search', 'stats', 'date_start', 'date_end'));
    }

    /**
     * Intervene on an order status.
     */
    public function updateStatus(Request $request, $id)
    {
        $order = Commande::findOrFail($id);

        $request->validate([
            'statut' => 'required|in:en_attente,en_preparation,pret,termine,annule',
            'raison_annulation' => 'required_if:statut,annule'
        ]);

        $order->statut = $request->statut;

        if ($request->statut === 'annule') {
            $order->date_annulation = now();
            $order->raison_annulation = $request->raison_annulation;
        }

        $order->save();

        // Optional: Notify both vendor and client
        // event(new \App\Events\OrderStatusChanged($order));

        return back()->with('status', 'Statut de la commande mis à jour par l\'administration.');
    }

    /**
     * Detailed view of an order.
     */
    public function show($id)
    {
        $order = Commande::with(['client', 'vendeur', 'lignes.plat', 'avis'])
            ->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }
}
