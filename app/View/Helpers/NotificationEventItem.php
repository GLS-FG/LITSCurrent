<?php

namespace App\View\Helpers;

class NotificationEventItem
{
    public string $title;
    public bool $done;

    public function __construct(string $title, bool $done) {
        $this->title = $title;
        $this->done = $done;
    }
}
