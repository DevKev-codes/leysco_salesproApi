<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class LeyscoHelpers
{
    public static function formatCurrency(float $amount): string
    {
        return 'KES ' . number_format($amount, 2) . ' /=';
    }

    public static function generateOrderNumber(): string
    {
        $latestId = \App\Models\Order::max('id') + 1;
        return 'ORD-' . now()->format('Y-m') . '-' . str_pad($latestId, 3, '0', STR_PAD_LEFT);
    }

    public static function calculateTax(float $amount, float $rate): float
    {
        return round(($amount * $rate) / 100, 2);
    }
}
