<?php

namespace App\Notifications;

use App\Models\DepartmentRequisition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequisitionRejected extends Notification implements ShouldQueue
{
    use Queueable;

    protected $requisition;
    protected $rejectionReason;
    protected $rejectedBy;

    public function __construct(DepartmentRequisition $requisition, $rejectionReason = null, $rejectedBy = null)
    {
        $this->requisition = $requisition;
        $this->rejectionReason = $rejectionReason;
        $this->rejectedBy = $rejectedBy;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $departmentName = $this->requisition->department->name ?? 'Your department';

        return (new MailMessage)
            ->subject('Requisition Rejected - ' . $this->requisition->requisition_number)
            ->greeting('Hello ' . ($notifiable->first_name ?? 'User') . '!')
            ->line('Your requisition has been **rejected** by management.')
            ->line('**Requisition Number:** ' . $this->requisition->requisition_number)
            ->line('**Department:** ' . $departmentName)
            ->line('**Rejected By:** ' . ($this->rejectedBy ?? 'Management'))
            ->line('**Rejection Reason:** ' . ($this->rejectionReason ?? 'No reason provided'))
            ->line('**Rejected Date:** ' . now()->format('F d, Y h:i A'))
            ->action('View Requisition', route('department.requisitions.show', $this->requisition->id))
            ->line('Please contact management if you have any questions or need to resubmit.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'requisition_rejected',
            'requisition_id' => $this->requisition->id,
            'requisition_number' => $this->requisition->requisition_number,
            'department_id' => $this->requisition->department_id,
            'department_name' => $this->requisition->department->name ?? 'N/A',
            'status' => 'rejected',
            'rejection_reason' => $this->rejectionReason,
            'message' => 'Your requisition ' . $this->requisition->requisition_number . ' has been rejected. Reason: ' . ($this->rejectionReason ?? 'No reason provided'),
            'action_url' => route('department.requisitions.show', $this->requisition->id),
            'rejected_by' => $this->rejectedBy,
            'rejected_at' => now()->toDateTimeString(),
        ];
    }

    public function toArray($notifiable)
    {
        return [
            'requisition_id' => $this->requisition->id,
            'requisition_number' => $this->requisition->requisition_number,
            'status' => 'rejected',
            'message' => 'Your requisition has been rejected.',
        ];
    }
}
