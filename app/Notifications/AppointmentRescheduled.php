<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Models\Appointment;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentRescheduled extends Notification
{
    use Queueable;
    private Appointment $appointment;

    /**
     * Create a new notification instance.
     */

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
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
    public function toDatabase(object $notifiable): array
    {
        return [
            "date" => $this->appointment->date,
            "time" => $this->appointment->time
        ];
    }
}
