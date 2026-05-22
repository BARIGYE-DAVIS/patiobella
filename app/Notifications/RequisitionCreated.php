<?php

namespace App\Notifications;

use App\Models\DepartmentRequisition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequisitionCreated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $requisition;
    protected $createdBy;

    public function __construct(DepartmentRequisition $requisition, $createdBy = null)
    {
        $this->requisition = $requisition;
        $this->createdBy = $createdBy;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $departmentName = $this->requisition->department->name ?? 'A department';

        return (new MailMessage)
            ->subject('New Requisition Created - ' . $this->requisition->requisition_number)
            ->greeting('Hello ' . ($notifiable->first_name ?? 'Manager') . '!')
            ->line('A new requisition has been created and is pending your approval.')
            ->line('**Requisition Number:** ' . $this->requisition->requisition_number)
            ->line('**Department:** ' . $departmentName)
            ->line('**Requested By:** ' . ($this->createdBy ?? 'Department staff'))
            ->line('**Date Created:** ' . $this->requisition->created_at->format('F d, Y h:i A'))
            ->action('Review Requisition', route('management.department-requisitions.show', $this->requisition->id))
            ->line('Please review and take appropriate action.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'requisition_created',
            'requisition_id' => $this->requisition->id,
            'requisition_number' => $this->requisition->requisition_number,
            'department_id' => $this->requisition->department_id,
            'department_name' => $this->requisition->department->name ?? 'N/A',
            'status' => 'pending',
            'message' => 'New requisition ' . $this->requisition->requisition_number . ' has been created by ' . ($this->createdBy ?? 'department staff') . ' and requires your approval.',
            'action_url' => route('management.department-requisitions.show', $this->requisition->id),
            'created_by' => $this->createdBy,
            'created_at' => $this->requisition->created_at->toDateTimeString(),
        ];
    }

    public function toArray($notifiable)
    {
        return [
            'requisition_id' => $this->requisition->id,
            'requisition_number' => $this->requisition->requisition_number,
            'status' => 'pending',
            'message' => 'New requisition requires your approval.',
        ];
    }
}
