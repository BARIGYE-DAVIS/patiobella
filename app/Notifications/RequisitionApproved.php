<?php

namespace App\Notifications;

use App\Models\DepartmentRequisition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequisitionApproved extends Notification implements ShouldQueue
{
    use Queueable;

    protected $requisition;
    protected $approvedBy;

    public function __construct(DepartmentRequisition $requisition, $approvedBy = null)
    {
        $this->requisition = $requisition;
        $this->approvedBy = $approvedBy;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $departmentName = $this->requisition->department->name ?? 'Your department';

        return (new MailMessage)
            ->subject('Requisition Approved - ' . $this->requisition->requisition_number)
            ->greeting('Hello ' . ($notifiable->first_name ?? 'User') . '!')
            ->line('Your requisition has been **approved** by management.')
            ->line('**Requisition Number:** ' . $this->requisition->requisition_number)
            ->line('**Department:** ' . $departmentName)
            ->line('**Approved By:** ' . ($this->approvedBy ?? 'Management'))
            ->line('**Approved Date:** ' . now()->format('F d, Y h:i A'))
            ->action('View Requisition', route('department.requisitions.show', $this->requisition->id))
            ->line('Please go to the store to pick up your requested items.')
            ->line('Thank you for using our system!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'requisition_approved',
            'requisition_id' => $this->requisition->id,
            'requisition_number' => $this->requisition->requisition_number,
            'department_id' => $this->requisition->department_id,
            'department_name' => $this->requisition->department->name ?? 'N/A',
            'status' => 'approved',
            'message' => 'Your requisition ' . $this->requisition->requisition_number . ' has been approved. Please go to the store to pick up your items.',
            'action_url' => route('department.requisitions.show', $this->requisition->id),
            'approved_by' => $this->approvedBy,
            'approved_at' => now()->toDateTimeString(),
        ];
    }

    public function toArray($notifiable)
    {
        return [
            'requisition_id' => $this->requisition->id,
            'requisition_number' => $this->requisition->requisition_number,
            'status' => 'approved',
            'message' => 'Your requisition has been approved.',
        ];
    }
}
