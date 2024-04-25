<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SavingResource extends JsonResource
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
            'account' => AccountResource::make($this->account),
            'save_goal' => $this->save_goal,
            'save_end_data' => $this->save_end_date,
            'schedule' => ScheduleResource::make($this->schedule)
        ];
    }
}
