<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PollResource extends JsonResource
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
            'status'=>$this->status,

            'start_time' => $this->start_time,
            'end_time' => $this->end_time,

            'voter_count' => $this->voter_count,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'options' => PollOptionResource::collection($this->options),
        ];
    }
}
