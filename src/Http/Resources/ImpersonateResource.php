<?php

namespace Octopy\Impersonate\Http\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Model $resource
 */
class ImpersonateResource extends JsonResource
{
    /**
     * @param  Request|null $request
     * @return array<string, mixed>
     */
    public function toArray($request) : array
    {
        $model = $this->resource;

        $key = method_exists($model, 'getKey') ? $model->getKey() : null;
        $val = method_exists($model, 'getImpersonateDisplayText') ? $model->getImpersonateDisplayText() : null;

        return [
            'key' => $key,
            'val' => $val,
        ];
    }
}
