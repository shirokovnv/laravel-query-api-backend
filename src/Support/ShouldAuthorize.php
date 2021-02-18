<?php

namespace Shirokovnv\LaravelQueryApiBackend\Support;

interface ShouldAuthorize
{
    public static function shouldAuthorizeAbilities(): array;
}
