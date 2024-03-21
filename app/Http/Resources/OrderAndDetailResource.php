<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderAndDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'customer_id' => $this->customer->id,
            'customer_name' => $this->customer->name,
            'date' => $this->date,
            'created_at' => $this->created_at->format('Y-m-d h:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d h:i:s'),
            'details' => $this->details->map(function ($detail){
                return [
                    'id' => $detail->id,
                    'book_id' => $detail->book->id,
                    'book_title' => $detail->book->title,
                    'qty' => $detail->qty
                ];
            }),

        ];
    }
}
