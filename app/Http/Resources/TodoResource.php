<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TodoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'memo' => $this->memo,
            'completed' => $this->completed ? true : false,
            'pinned' => $this->pinned ? true : false,
            'time_stamps' => [
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at
            ]
        ];
    }
}
