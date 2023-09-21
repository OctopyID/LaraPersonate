<?php

namespace Octopy\Impersonate\Contracts;

interface Impersonation
{
    /**
     * @return array
     */
    public function getImpersonateSearchField() : array;

    /**
     * @return string
     */
    public function getImpersonateDisplayText() : string;
}
