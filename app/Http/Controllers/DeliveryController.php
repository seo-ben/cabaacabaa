<?php

namespace App\Http\Controllers;

use App\Models\DeliveryRequest;
use App\Models\DeliveryApplication;
use App\Models\Commande;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryController extends Controller
{
    /**
     * Show list of delivery requests for potential drivers
     */
    public function index(Request $request)
    {
        $query = DeliveryRequest::where('status', 'open')
<<<<<<< Updated upstream
=======
            ->has('vendeur')
>>>>>>> Stashed changes
            ->with(['vendeur.zone']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('vendeur', function($q) use ($search) {
                $q->where('nom_commercial', 'like', "%{$search}%");
            });
        }

        if ($request->filled('zone')) {
            $zoneId = $request->zone;
            $query->whereHas('vendeur', function($q) use ($zoneId) {
                $q->where('id_zone', $zoneId);
            });
        }

        $requests = $query->latest()
            ->paginate(12);

        $zones = \App\Models\ZoneGeographique::all();

        return view('delivery.index', compact('requests', 'zones'));
    }

    /**
     * Handle delivery application submission
     */
    public function apply(Request $request, $id)
    {
        $deliveryRequest = DeliveryRequest::findOrFail($id);
        
        // Prevent duplicate applications
        $exists = DeliveryApplication::where('id_delivery_request', $id)
            ->where('id_user', Auth::id())
            ->exists();

        if ($exists) {
            return back()->with('error', 'Vous avez déjà postulé pour ce vendeur.');
        }

        DeliveryApplication::create([
            'id_delivery_request' => $id,
            'id_user' => Auth::id(),
            'status' => 'pending',
            'notes' => $request->notes
        ]);

        return back()->with('success', 'Votre candidature a été envoyée avec succès.');
    }

    /**
     * Show assigned deliveries for the driver
     */
    public function myDeliveries()
    {
        $deliveries = Commande::where('id_livreur', Auth::id())
            ->with('vendeur')
            ->orderBy('date_commande', 'desc')
            ->paginate(15);

        return view('delivery.my-deliveries', compact('deliveries'));
    }

    /**
     * Vendor: Show delivery management page
     */
    public function vendorIndex($vendor_slug)
    {
        $vendeur = Auth::user()->vendeur;
        $activeRequest = DeliveryRequest::where('id_vendeur', $vendeur->id_vendeur)
            ->where('status', 'open')
            ->first();

        $applications = [];
        if ($activeRequest) {
            $applications = DeliveryApplication::where('id_delivery_request', $activeRequest->id)
                ->with('user')
                ->get();
        }

        $acceptedDrivers = DeliveryApplication::whereHas('request', function($q) use ($vendeur) {
                $q->where('id_vendeur', $vendeur->id_vendeur);
            })
            ->where('status', 'accepted')
            ->with('user')
            ->get();

        return view('vendors.delivery.index', compact('vendeur', 'activeRequest', 'applications', 'acceptedDrivers'));
    }

    /**
     * Vendor: Toggle or create delivery request
     */
    public function storeRequest(Request $request, $vendor_slug)
    {
        $vendeur = Auth::user()->vendeur;
        
        $deliveryRequest = DeliveryRequest::updateOrCreate(
            ['id_vendeur' => $vendeur->id_vendeur, 'status' => 'open'],
            ['message' => $request->message]
        );

        return back()->with('success', 'Votre demande de livreur a été mise à jour.');
    }

    /**
     * Vendor: Close delivery request
     */
    public function closeRequest($vendor_slug)
    {
        $vendeur = Auth::user()->vendeur;
        DeliveryRequest::where('id_vendeur', $vendeur->id_vendeur)
            ->where('status', 'open')
            ->update(['status' => 'closed']);

        return back()->with('success', 'Demande de livreur clôturée.');
    }

    /**
     * Vendor: Accept/Reject application
     */
    public function handleApplication($vendor_slug, $id, $action)
    {
        $application = DeliveryApplication::findOrFail($id);
        $status = ($action === 'accept') ? 'accepted' : 'rejected';
        
        $application->update(['status' => $status]);

        // Notify user
        $vendeur = $application->request->vendeur;
        $message = "Votre candidature pour être livreur chez {$vendeur->nom_commercial} a été " . ($action === 'accept' ? 'acceptée' : 'refusée') . ".";
        
        Notification::create([
            'id_utilisateur' => $application->id_user,
            'type_notification' => 'system',
            'titre' => 'Candidature Livreur',
            'message' => $message,
            'id_vendeur' => $vendeur->id_vendeur,
            'date_creation' => now()
        ]);

        return back()->with('success', 'Candidature traitée.');
    }

    /**
     * Vendor: Assign order to driver
     */
    public function assignOrder(Request $request, $vendor_slug, $orderId)
    {
        $commande = Commande::findOrFail($orderId);
        $commande->update(['id_livreur' => $request->id_livreur]);

        // Notify driver
        Notification::create([
            'id_utilisateur' => $request->id_livreur,
            'type_notification' => 'commande',
            'titre' => 'Nouvelle Livraison',
            'message' => "Une nouvelle commande (#{$commande->numero_commande}) vous a été assignée.",
            'id_commande' => $commande->id_commande,
            'id_vendeur' => $commande->id_vendeur,
            'date_creation' => now()
        ]);

        return back()->with('success', 'Livreur assigné à la commande.');
    }

    /**
     * Driver: Mark a delivery as completed
     */
    public function completeDelivery($orderId)
    {
        $commande = Commande::where('id_livreur', Auth::id())
            ->findOrFail($orderId);

        $commande->update([
            'statut' => 'termine',
            'heure_recuperation_effective' => now()
        ]);

        $receiptUrl = route('order.receipt', ['code' => $commande->numero_commande]);

        // Notify customer
        Notification::create([
            'id_utilisateur' => $commande->id_client,
            'type_notification' => 'commande',
            'titre' => 'Commande Livrée',
            'message' => "Votre commande #{$commande->numero_commande} a été livrée avec succès. Bon appétit ! Consultez votre reçu ici : {$receiptUrl}",
            'id_commande' => $commande->id_commande,
            'id_vendeur' => $commande->id_vendeur,
            'date_creation' => now()
        ]);

        // Notify vendor
        Notification::create([
            'id_utilisateur' => $commande->vendeur->id_user,
            'type_notification' => 'commande',
            'titre' => 'Livraison terminée',
            'message' => "La commande #{$commande->numero_commande} a été marquée comme livrée par le partenaire.",
            'id_commande' => $commande->id_commande,
            'id_vendeur' => $commande->id_vendeur,
            'date_creation' => now()
        ]);

        return back()->with('success', 'Livraison confirmée !');
    }
}
