<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InsufficientBalanceNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public $accountId,
        public $remainingMonthlySavingId,
        public $difference,
        public $numberOfRemainingMonthlySavings,
        public $amountToAdd
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)->markdown('mail.save-goal.insufficientBalance', [
            'accountId' => $this->accountId,
            'monthlySavingId' => $this->remainingMonthlySavingId,
            'difference' => $this->difference,
            'numberOfRemainingSaving' => $this->numberOfRemainingMonthlySavings,
            'amountToAdd' => $this->amountToAdd
        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
