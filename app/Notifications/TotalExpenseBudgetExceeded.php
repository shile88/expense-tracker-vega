<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TotalExpenseBudgetExceeded extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $totalExpense, public $expenseBudgetStartDate, public $expenseBudgetEndDate, public $expense, public $accountId, public $accountBudget)
    {
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
        return (new MailMessage)->markdown('mail.budget.totalExpense', [
            'totalExpense' => $this->totalExpense,
            'expenseBudgetStartDate' => $this->expenseBudgetStartDate,
            'expenseBudgetEndDate' => $this->expenseBudgetEndDate,
            'expense' => $this->expense->amount,
            'accountId' => $this->accountId,
            'accountBudget' => $this->accountBudget
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
