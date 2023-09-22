<?php

namespace Octopy\Impersonate\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class ImpersonateCollection extends ResourceCollection
{
    /**
     * @param  Request $request
     * @return array
     */
    public function toArray(Request $request) : array
    {
        return [
            'data' => $this->collection->filter(fn($row) => app('impersonate.authorization')->isImpersonated($row))->values(),
        ];
    }
}
