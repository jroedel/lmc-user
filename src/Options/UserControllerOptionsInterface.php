<?php

declare(strict_types=1);

namespace LmcUser\Options;

interface UserControllerOptionsInterface
{
    /**
     * set use redirect param if present
     *
     * @param bool $useRedirectParameterIfPresent
     */
    public function setUseRedirectParameterIfPresent($useRedirectParameterIfPresent);

    /**
     * get use redirect param if present
     *
     * @return bool
     */
    public function getUseRedirectParameterIfPresent();
}
