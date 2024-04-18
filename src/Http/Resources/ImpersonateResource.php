<?php

namespace Octopy\Impersonate\Http\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Octopy\Impersonate\Concerns\HasImpersonation;

/**
 * @mixin Model
 * @mixin HasImpersonation
 */
class ImpersonateResource extends JsonResource
{
    /**
     * @param  Request|null $request
     * @return array
     */
    public function toArray($request) : array
    {
        return [
            'key' => $this->getKey(),
            'val' => $this->getImpersonateDisplayText(),
        ];
    }
}
