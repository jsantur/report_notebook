<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait HasMonitoringSession
{
    /**
     * Obtiene el ID del usuario objetivo.
     * Si es Admin, obtiene el ID del creador del borrador activo si hay una sesión activa de monitoreo.
     */
    protected function getTargetUserId()
    {
        $userId = Auth::id();
        if (Auth::check() && Auth::user()->role === 'admin') {
            if (session()->has('admin_monitoring_user_id')) {
                return session('admin_monitoring_user_id');
            }
            return $userId;
        }
        return $userId;
    }
}
