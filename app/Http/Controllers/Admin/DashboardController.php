<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get current month statistics - only count paid orders
        $totalRevenue = Order::where('payment_status', 'paid')
            ->sum('total_amount');
        
        $totalOrders = Order::count();
        
        $totalProducts = Product::count();
        $activeProducts = Product::where('status', 'active')->count();
        
        $totalCustomers = User::count();
        
        // Calculate growth percentages compared to last month
        $lastMonthRevenue = Order::where('payment_status', 'paid')
            ->whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->sum('total_amount');
        
        $currentMonthRevenue = Order::where('payment_status', 'paid')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total_amount');
        
        $revenueGrowth = $lastMonthRevenue > 0 
            ? round((($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : 0;
        
        $lastMonthOrders = Order::whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->count();
        
        $currentMonthOrders = Order::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();
        
        $ordersGrowth = $lastMonthOrders > 0 
            ? round((($currentMonthOrders - $lastMonthOrders) / $lastMonthOrders) * 100, 1)
            : 0;
        
        $lastMonthCustomers = User::whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->count();
        
        $currentMonthCustomers = User::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();
        
        $customersGrowth = $lastMonthCustomers > 0 
            ? round((($currentMonthCustomers - $lastMonthCustomers) / $lastMonthCustomers) * 100, 1)
            : 0;
        
        // Get recent orders
        $recentOrders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Get top products (you'll need to add a sales_count column or calculate it)
        $topProducts = Product::with('category')
            ->withCount(['orderItems as sales_count' => function ($query) {
                $query->select(DB::raw('SUM(quantity)'));
            }])
            ->orderBy('sales_count', 'desc')
            ->limit(5)
            ->get();

        // Get sales data for charts
        // 7 days data
        $sevenDaysData = $this->getSalesDataForDays(7);
        
        // 30 days data (grouped by 5-day intervals)
        $thirtyDaysData = $this->getSalesDataForDays(30, 5);
        
        // 12 months data
        $twelveMonthsData = $this->getSalesDataForMonths(12);

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'totalProducts',
            'activeProducts',
            'totalCustomers',
            'revenueGrowth',
            'ordersGrowth',
            'customersGrowth',
            'recentOrders',
            'topProducts',
            'sevenDaysData',
            'thirtyDaysData',
            'twelveMonthsData'
        ));
    }

    /**
     * Get sales data for the last N days
     */
    private function getSalesDataForDays($days, $groupByDays = 1)
    {
        $labels = [];
        $data = [];
        
        if ($groupByDays === 1) {
            // For 7 days - show each day individually
            $dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = now()->subDays($i)->startOfDay();
                $dayOfWeek = $date->dayOfWeek;
                $labels[] = $dayNames[$dayOfWeek];
                
                $sales = Order::where('payment_status', 'paid')
                    ->whereDate('created_at', $date)
                    ->sum('total_amount');
                
                $data[] = (float) $sales;
            }
        } else {
            // For 30 days - group by intervals
            $intervals = ceil($days / $groupByDays);
            
            for ($i = $intervals - 1; $i >= 0; $i--) {
                $endDate = now()->subDays($i * $groupByDays)->endOfDay();
                $startDate = now()->subDays(($i + 1) * $groupByDays)->startOfDay();
                
                // Label with day number (approximate)
                $dayNumber = ($intervals - $i - 1) * $groupByDays + 1;
                $labels[] = (string) $dayNumber;
                
                $sales = Order::where('payment_status', 'paid')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->sum('total_amount');
                
                $data[] = (float) $sales;
            }
        }
        
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Get sales data for the last N months
     */
    private function getSalesDataForMonths($months)
    {
        $labels = [];
        $data = [];
        
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthIndex = (int) $date->format('n') - 1; // 1-12 to 0-11
            $labels[] = $monthNames[$monthIndex];
            
            $sales = Order::where('payment_status', 'paid')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('total_amount');
            
            $data[] = (float) $sales;
        }
        
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
}
