<?php

namespace App\Modules\Notifications\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
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
            'id' => $this->id,
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'type_name' => $this->type_name,
            'priority' => $this->priority,
            'priority_name' => $this->priority_name,
            'is_read' => $this->is_read,
            'data' => $this->data,
            'action_url' => $this->action_url,
            'time_ago' => $this->time_ago,
            'created_at' => $this->created_at->toISOString(),
            'read_at' => $this->read_at ? $this->read_at->toISOString() : null,
        ];
    }
}
