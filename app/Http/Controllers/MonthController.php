<?php

namespace App\Http\Controllers;

use App\Models\Month;
use App\Models\Order;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Contrôleur pour gérer les statistiques mensuelles des commandes.
 */
class MonthController extends Controller
{
    /**
     * Met à jour les statistiques pour un mois spécifique.
     *
     * @param  string|null  $month  Le mois au format mm (01-12)
     * @param  string|null  $year  L'année au format YYYY
     */
    public static function updateMonthStats(?string $month = null, ?string $year = null): void
    {
        Log::channel('stats')->info('Début de la mise à jour des statistiques mensuelles');

        // Si le mois ou l'année ne sont pas spécifiés, utiliser le mois/année en cours
        if (! $month || ! $year) {
            $date = Carbon::now();
            $month = $date->format('m');
            $year = $date->format('Y');
            Log::channel('stats')->info("Utilisation du mois/année en cours: $month/$year");
        }

        // Compter le nombre de commandes livrées pour le mois/année spécifié
        $count = Order::query()
            ->whereYear('delivered_date', $year)
            ->whereMonth('delivered_date', $month)
            ->where('status', '=', 'livré')
            ->count();

        Log::channel('stats')->info("Nombre de commandes livrées pour $month/$year: $count");

        // Créer ou mettre à jour l'enregistrement des statistiques mensuelles
        Month::updateOrCreate(
            [
                'year' => $year,
                'month_number' => $month,
            ],
            [
                'month' => Carbon::createFromDate($year, $month, 1)->locale('fr')->monthName,
                'count' => $count,
                'report_created_at' => now(),
            ]
        );

        Log::channel('stats')->info('Statistiques mensuelles mises à jour avec succès');
    }

    /**
     * Initialise les statistiques pour tous les mois ayant des commandes.
     */
    public static function initializeAllMonths(): void
    {
        Log::channel('stats')->info('Début de l\'initialisation des statistiques pour tous les mois');

        // Récupérer tous les mois distincts où il y a des commandes
        $months = Order::query()
            ->whereNotNull('delivered_date')
            ->where('status', '=', 'livré')
            ->selectRaw('DISTINCT EXTRACT(YEAR FROM delivered_date) as year')
            ->selectRaw('EXTRACT(MONTH FROM delivered_date) as month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        Log::channel('stats')->info('Nombre de mois à traiter: ' . $months->count());

        // Mettre à jour les statistiques pour chaque mois
        foreach ($months as $monthData) {
            $formattedMonth = str_pad($monthData->month, 2, '0', STR_PAD_LEFT);
            Log::channel('stats')->info("Traitement du mois $formattedMonth/{$monthData->year}");

            self::updateMonthStats(
                $formattedMonth,
                $monthData->year
            );
        }

        Log::channel('stats')->info('Initialisation des statistiques terminée');
    }

    /**
     * Initialise les statistiques pour le mois en cours et le mois précédent.
     */
    public static function initializeCurrentAndLastMonth(): void
    {
        Log::channel('stats')->info('Début de l\'initialisation des statistiques pour le mois courant et le mois précédent');

        $currentDate = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        // Mettre à jour les stats du mois courant
        Log::channel('stats')->info('Mise à jour des statistiques pour le mois courant: ' . $currentDate->format('m/Y'));
        self::updateMonthStats(
            $currentDate->format('m'),
            $currentDate->format('Y')
        );

        // Mettre à jour les stats du mois précédent
        Log::channel('stats')->info('Mise à jour des statistiques pour le mois précédent: ' . $lastMonth->format('m/Y'));
        self::updateMonthStats(
            $lastMonth->format('m'),
            $lastMonth->format('Y')
        );

        Log::channel('stats')->info('Initialisation terminée');
    }

    /**
     * Point d'entrée pour la mise à jour des statistiques mensuelles.
     */
    public function month(): void
    {
        Log::channel('stats')->info('Démarrage de la mise à jour des statistiques mensuelles');
        $this->initializeCurrentAndLastMonth();
        Log::channel('stats')->info('Fin de la mise à jour des statistiques mensuelles');
    }
}
