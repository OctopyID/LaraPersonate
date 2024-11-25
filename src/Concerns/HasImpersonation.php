<?php

namespace Octopy\Impersonate\Concerns;

use Illuminate\Support\Facades\App;
use Octopy\Impersonate\Authorization;
use Octopy\Impersonate\Exceptions\ImpersonateException;
use Octopy\Impersonate\Impersonate;

trait HasImpersonation
{
    /**
     * @return void
     */
    public static function bootHasImpersonation() : void
    {
        (new static)->setImpersonateAuthorization(App::make(
            'impersonate.authorization'
        ));
    }

    /**
     * @param  mixed $impersonated
     * @return Impersonate
     * @throws ImpersonateException
     */
    public function impersonate(mixed $impersonated = null) : Impersonate
    {
        $manager = App::make('impersonate');

        if ($impersonated) {
            $manager->begin($this, $impersonated);
        }

        return $manager;
    }

    /**
     * @param  Authorization $authorization
     * @return void
     * @codeCoverageIgnore
     */
    public function setImpersonateAuthorization(Authorization $authorization) : void
    {
        $authorization->impersonator(function () {
            return true;
        });

        $authorization->impersonated(function () {
            return true;
        });
    }
}
