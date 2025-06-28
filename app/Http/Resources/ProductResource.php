<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'cost_price' => $this->cost_price,
            'reorder_level' => $this->reorder_level,
            'is_active' => $this->is_active,
            'category' => $this->whenLoaded('category'),
            'inventory' => InventoryResource::collection($this->whenLoaded('inventory')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
