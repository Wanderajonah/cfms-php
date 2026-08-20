<?php

declare(strict_types=1);

final class NotificationController
{
    public function apiAdmin(): void
    {
        Auth::requireApiRole('admin');
        Response::json((new Feedback())->adminNotifications());
    }
}
