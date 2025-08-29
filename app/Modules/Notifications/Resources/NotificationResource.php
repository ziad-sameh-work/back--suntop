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
            'body' => $this->body,
            'type' => $this->type,
            'type_name' => $this->type_name,
            'alert_type' => $this->alert_type,
            'alert_type_name' => $this->alert_type_name,
            'target_type' => $this->target_type,
            'target_type_name' => $this->target_type_name,
            'priority' => $this->priority,
            'priority_name' => $this->priority_name,
            'is_read' => $this->is_read,
            'data' => $this->data,
            'action_url' => $this->action_url,
            'time_ago' => $this->time_ago,
            'user_category' => $this->when($this->userCategory, [
                'id' => $this->userCategory?->id,
                'name' => $this->userCategory?->display_name,
            ]),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'read_at' => $this->read_at ? $this->read_at->toISOString() : null,
            'formatted_date' => $this->created_at->format('Y-m-d'),
            'formatted_time' => $this->created_at->format('H:i'),
        ];
    }
}
