<?php

namespace Octopy\Impersonate\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class ImpersonateCollection extends ResourceCollection
{
    /**
     * @param  Request $request
     * @return array<string, \Illuminate\Support\Collection<int, mixed>>
     */
    public function toArray(Request $request) : array
    {
        $collection = $this->collection;
        if (! $collection instanceof Collection) {
            /** @var iterable<int, mixed> $items */
            $items = $collection;
            /** @var \Illuminate\Support\Collection<int, mixed> $collection */
            $collection = collect($items);
        }

        return [
            'data' => $collection,
        ];
    }
}
