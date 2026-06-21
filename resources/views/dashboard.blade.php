@extends('layouts.app')

@section('title', 'Dashboard Administrativo - Seguridad Ciudadana')

@section('content')
<div class="space-y-8" x-data="dashboardUI()">
    
    <!-- Cabecera Premium (Estilo Kilometrajes) -->
    <div class="bg-white rounded-2xl shadow-sm p-5 mb-8 border border-gray-100 flex flex-col lg:flex-row justify-between items-center space-y-4 lg:space-y-0">
        <!-- Izquierda: Identidad -->
        <div class="flex items-center space-x-5">
            <div class="bg-blue-600 p-4 rounded-2xl text-white shadow-lg shadow-blue-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight uppercase">Dashboard Principal</h1>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Monitorización y Análisis Operativo</p>
            </div>
        </div>

        <div class="bg-slate-50 p-2 rounded-2xl flex items-center border border-slate-100 shadow-inner">
            <button @click="view = 'daily'; updateChart()" 
                :class="view === 'daily' ? 'bg-blue-600 text-white shadow-lg shadow-blue-100 scale-105' : 'text-slate-500 hover:text-slate-700'"
                class="px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] transition-all duration-300">
                Vista Diaria
            </button>
            <button @click="view = 'monthly'; updateChart()" 
                :class="view === 'monthly' ? 'bg-blue-600 text-white shadow-lg shadow-blue-100 scale-105' : 'text-slate-500 hover:text-slate-700'"
                class="px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] transition-all duration-300">
                Vista Mensual
            </button>
        </div>
    </div>

    <!-- Panel Central: Gráfico -->
    <div class="bg-white rounded-[40px] p-10 shadow-sm border border-slate-100">
        <div class="flex justify-between items-center mb-10">
            <div>
                <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Reportes por Día y Mes</h3>
                <div class="flex items-center space-x-6 mt-4">
                    <div class="flex items-center space-x-2" x-show="view === 'daily'" x-cloak>
                        <div class="w-3 h-3 bg-blue-500 rounded-sm"></div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Reportes por Día</span>
                    </div>
                    <div class="flex items-center space-x-2" x-show="view === 'monthly'" x-cloak>
                        <div class="w-3 h-3 bg-emerald-500 rounded-sm"></div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Reportes por Mes (Total Anual)</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="h-[400px] w-full relative">
            <canvas id="mainDashboardChart"></canvas>
        </div>
    </div>



    <!-- Footer del Dashboard -->
    <div class="pt-10 flex justify-center md:justify-end">
        <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em]">Proyecto Marte v1.0 -  Joseph Santur M. ヾ(⌐■_■)ノ.</p>
    </div>
</div>

<script>
    function dashboardUI() {
        return {
            view: 'daily',
            chart: null,
            dailyLabels: @json($daily_labels),
            monthlyLabels: @json($monthly_labels),
            dailyData: @json($daily_data),
            monthlyData: @json($monthly_data),

            init() {
                this.initMainChart();
            },

            initMainChart() {
                const ctx = document.getElementById('mainDashboardChart').getContext('2d');
                const isMonthly = this.view === 'monthly';

                this.chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: isMonthly ? this.monthlyLabels : this.dailyLabels,
                        datasets: [
                            {
                                label: 'Reportes',
                                data: isMonthly ? this.monthlyData : this.dailyData,
                                backgroundColor: isMonthly ? '#10b981' : '#2563eb',
                                borderRadius: 6,
                                barThickness: isMonthly ? 50 : 12
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                padding: 12,
                                bodyFont: { weight: 'bold' },
                                titleFont: { weight: 'black' },
                                callbacks: {
                                    label: function(context) {
                                        return context.parsed.y + ' reportes';
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { 
                                    display: true,
                                    color: 'rgba(226, 232, 240, 0.8)',
                                    drawBorder: false,
                                    borderDash: [5, 5]
                                },
                                ticks: { font: { weight: 'black', size: 9 }, color: '#94a3b8' }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: { 
                                    stepSize: 1,
                                    font: { weight: 'bold', size: 10 },
                                    color: isMonthly ? '#10b981' : '#2563eb'
                                },
                                grid: { 
                                    display: true,
                                    color: 'rgba(226, 232, 240, 0.8)',
                                    borderDash: [5, 5]
                                }
                            }
                        }
                    }
                });
            },

            updateChart() {
                if (this.chart) {
                    this.chart.destroy();
                }
                this.initMainChart();
            }
        }
    }
</script>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
