<?php

namespace Octopy\Impersonate\Contracts;

interface HasImpersonationUI
{
    /**
     * @return string[]
     */
    public function getImpersonateSearchField() : array;

    /**
     * @return string
     */
    public function getImpersonateDisplayText() : string;
}
