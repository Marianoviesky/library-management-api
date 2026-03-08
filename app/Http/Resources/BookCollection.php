<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BookCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'itemsReceived' => $this->collection->count(),
            'curPage' => $this->currentPage(),
            'nextPage' => $this->hasMorePages() ? $this->currentPage() + 1 : null,
            'itemsTotal' => $this->total(),
            'pageTotal' => $this->lastPage(),
            'items' => BookResource::collection($this->collection),
        ];
    }
}
