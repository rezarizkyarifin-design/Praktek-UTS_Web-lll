<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Karyawan;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class KaryawanPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Karyawan');
    }

    public function view(AuthUser $authUser, Karyawan $karyawan): bool
    {
        return $authUser->can('View:Karyawan');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Karyawan');
    }

    public function update(AuthUser $authUser, Karyawan $karyawan): bool
    {
        return $authUser->can('Update:Karyawan');
    }

    public function delete(AuthUser $authUser, Karyawan $karyawan): bool
    {
        return $authUser->can('Delete:Karyawan');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Karyawan');
    }

    public function restore(AuthUser $authUser, Karyawan $karyawan): bool
    {
        return $authUser->can('Restore:Karyawan');
    }

    public function forceDelete(AuthUser $authUser, Karyawan $karyawan): bool
    {
        return $authUser->can('ForceDelete:Karyawan');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Karyawan');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Karyawan');
    }

    public function replicate(AuthUser $authUser, Karyawan $karyawan): bool
    {
        return $authUser->can('Replicate:Karyawan');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Karyawan');
    }
}
