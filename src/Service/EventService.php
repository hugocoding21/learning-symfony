<?php

namespace App\Service;

use DateTime;

class EventService
{
    public $events = [];
    public function __construct()
    {
        $this->events = [
            ['id' => 1, 'name' => 'Cinema', 'startAt' => new DateTime('2024-02-21 09:30:00'), 'endAt' => new DateTime('2024-02-21 11:00:00')],
            ['id' => 2, 'name' => 'Theatre', 'startAt' => new DateTime('2024-01-21 18:20:00'), 'endAt' => new DateTime('2024-01-21 20:00:00')],
            ['id' => 3, 'name' => 'Concert', 'startAt' => new DateTime('2024-01-21 14:00:00'), 'endAt' => new DateTime('2024-05-21 17:00:00')],
        ];
    }

    public function all()
    {
        return $this->events;
    }


    public function find($id)
    {
        foreach ($this->events as $event) {
            if ($event['id'] == $id) {
                return $event;
            }
        }
    }
}
