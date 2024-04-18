<?php

namespace Octopy\Impersonate\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ImpersonateCollection extends ResourceCollection
{
    /**
     * @param  Request $request
     * @return array
     */
    public function toArray(Request $request) : array
    {
        return [
            'data' => $this->collection->filter(function (ImpersonateResource $row) {
                return app('impersonate.authorization')->isImpersonated($row->resource);
            })->values(),
        ];
    }
}
