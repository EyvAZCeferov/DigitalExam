<?php

namespace App\Traits;

trait HasPermission
{
    public function hasPermissionFor(string $permission): bool
    {
        return in_array($permission, $this->role->permissions);
    }
}
