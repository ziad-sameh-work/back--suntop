<?php

namespace App\Modules\Admin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryStatisticsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'category' => new UserCategoryResource($this->resource['category']),
            'user_count' => $this->resource['user_count'],
            'total_purchases' => number_format($this->resource['total_purchases'], 2),
            'average_purchase' => number_format($this->resource['average_purchase'], 2),
            'formatted_total_purchases' => number_format($this->resource['total_purchases'], 0) . ' جنيه',
            'formatted_average_purchase' => number_format($this->resource['average_purchase'], 0) . ' جنيه',
        ];
    }
}
