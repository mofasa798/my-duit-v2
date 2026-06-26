<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReportPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Report $report): bool
    {
        return $user->id === $report->user_id;
    }

    public function delete(User $user, Report $report): bool
    {
        return $user->id === $report->user_id;
    }
}
