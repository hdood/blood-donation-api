<?php

namespace App\Notifications;

use App\Models\BloodRequest;
use App\Models\Patient;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BloodRequested extends Notification
{
    use Queueable;



    /**
     * Create a new notification instance.
     */
    public function __construct(private Patient $patient, private BloodRequest $request)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'patient' => $this->patient,
            'request' => $this->request
        ];
    }
}
