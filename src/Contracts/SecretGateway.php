<?php

namespace audunru\ConfigSecrets\Contracts;

use Illuminate\Support\Collection;

interface SecretGateway
{
    /**
     * Retrieve secrets.
     */
    public function getSecrets(): Collection;
}
