<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Voiture;
use App\Http\Requests\VoitureStoreRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class VoitureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Récupérer toutes les voitures
            $voitures = Voiture::all();

            return response()->json([
                'voitures' => $voitures
            ], 200);
        } catch (\Exception $e) {
            // En cas d'erreur, retourner une réponse JSON
            return response()->json([
                'message' => "Une erreur s'est produite!"
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VoitureStoreRequest $request)
    {
        try {
            // Générer un nom aléatoire pour l'image
            $imageName = Str::random(32) . "." . $request->image->getClientOriginalExtension();

            // Créer une nouvelle voiture dans la base de données
            $voiture = Voiture::create([
                'marque' => $request->marque,
                'modele' => $request->modele,
                'type' => $request->type,
                'matricule' => $request->matricule,
                'VIN' => $request->VIN,
                'image' => $imageName,
                'date_de_vignette' => $request->date_de_vignette,
                'date_d_assurance' => $request->date_d_assurance,
            ]);

            // Enregistrer l'image dans le stockage public
            Storage::disk('public')->put($imageName, file_get_contents($request->image));

            return response()->json([
                'message' => "Véhicule ajouté avec succès",
                'voiture' => $voiture,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => "Une erreur s'est produite!"
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            // Récupérer une voiture spécifique par son ID
            $voiture = Voiture::find($id);

            if (!$voiture) {
                return response()->json([
                    'message' => 'La voiture n\'existe pas!'
                ], 404);
            }

            return response()->json([
                'voiture' => $voiture
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => "Une erreur s'est produite!"
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VoitureStoreRequest $request, $id)
    {
        try {
            // Récupérer la voiture par son ID
            $voiture = Voiture::find($id);

            if (!$voiture) {
                return response()->json([
                    'message' => 'La voiture n\'existe pas'
                ], 404);
            }

            // Mettre à jour les propriétés de la voiture avec les nouvelles valeurs
            $voiture->update([
                'marque' => $request->marque,
                'modele' => $request->modele,
                'type' => $request->type,
                'matricule' => $request->matricule,
                'VIN' => $request->VIN,
                'date_de_vignette' => $request->date_de_vignette,
                'date_d_assurance' => $request->date_d_assurance,
            ]);

            // Vérifier si une nouvelle image est fournie
            if ($request->image) {
                // Stockage public
                $storage = Storage::disk('public');

                // Supprimer l'ancienne image si elle existe
                if ($storage->exists($voiture->image)) {
                    $storage->delete($voiture->image);
                }

                // Générer un nouveau nom d'image
                $imageName = Str::random(32) . "." . $request->image->getClientOriginalExtension();

                // Définir le nouveau nom d'image dans le modèle de la voiture
                $voiture->update(['image' => $imageName]);

                // Enregistrer la nouvelle image dans le dossier public
                $storage->put($imageName, file_get_contents($request->image));
            }

            return response()->json([
                'message' => 'Voiture modifiée avec succès',
                'voiture' => $voiture,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => "Une erreur s'est produite!"
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Récupérer la voiture par son ID
            $voiture = Voiture::find($id);

            if (!$voiture) {
                return response()->json([
                    'message' => 'La voiture n\'existe pas'
                ], 404);
            }

            // Supprimer l'image associée du stockage
            Storage::disk('public')->delete($voiture->image);

            // Supprimer la voiture de la base de données
            $voiture->delete();

            return response()->json([
                'message' => 'Voiture supprimée avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => "Une erreur s'est produite!"
            ], 500);
        }
    }
}
