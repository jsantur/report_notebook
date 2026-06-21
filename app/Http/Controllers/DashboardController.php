<?php

namespace App\Http\Controllers;

use App\Models\Reporte;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // 1. Datos Diarios (Mes Actual)
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        
        $reportsPerDay = Reporte::whereBetween('fecha', [$startOfMonth, $endOfMonth])
            ->selectRaw('DATE(fecha) as fecha_real, COUNT(*) as count')
            ->groupBy('fecha_real')
            ->get()
            ->pluck('count', 'fecha_real');

        $daysInMonth = now()->daysInMonth;
        $daily_labels = [];
        $daily_data = [];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $currentDate = $startOfMonth->copy()->addDays($i - 1);
            $dateString = $currentDate->format('Y-m-d');
            $daily_labels[] = $currentDate->format('M d');
            $daily_data[] = $reportsPerDay->get($dateString, 0);
        }

        // 2. Datos Mensuales (Año Actual)
        $startOfYear = now()->startOfYear();
        $endOfYear = now()->endOfYear();

        $reportsPerMonth = Reporte::whereBetween('fecha', [$startOfYear, $endOfYear])
            ->selectRaw("strftime('%m', fecha) as month, COUNT(*) as count")
            ->groupBy('month')
            ->get()
            ->pluck('count', 'month');

        $monthly_labels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $monthly_data = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthKey = str_pad($m, 2, '0', STR_PAD_LEFT);
            $monthly_data[] = $reportsPerMonth->get($monthKey, 0);
        }

        return view('dashboard', compact(
            'daily_labels',
            'daily_data',
            'monthly_labels',
            'monthly_data'
        ));
    }
}
