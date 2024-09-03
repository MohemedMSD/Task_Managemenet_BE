<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'status' => TaskStatus::select('id', 'name', 'disabled')->find($this->statu_id),
            'finished_at' => substr($this->finished_at, 0, 10),
            'user' => User::select('id', 'name', 'email')->find($this->user_id)
        ];
    }
}
