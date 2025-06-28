<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\Inventory;

class WarehouseController extends Controller
{
    public function index()
    {
        return Warehouse::all();
    }

    public function inventory($id)
    {
        return Inventory::where('warehouse_id', $id)->with('product')->get();
    }
}

