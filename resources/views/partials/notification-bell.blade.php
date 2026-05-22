{{-- Global Notification Bell - Works for all departments --}}
<div class="relative ml-3">
    <button id="globalNotificationDropdown" class="relative p-1 text-gray-400 hover:text-gray-500 focus:outline-none transition-colors">
        <i class="fas fa-bell text-xl"></i>
        @php
            $unreadCount = Auth::user()->unreadNotifications->count();
        @endphp
        @if($unreadCount > 0)
            <span class="absolute top-0 right-0 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white ring-2 ring-white">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div id="globalNotificationPanel" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
        <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700">Notifications</h3>
            <button onclick="markAllNotificationsRead()" class="text-xs text-blue-600 hover:underline">Mark all read</button>
        </div>

        <div class="max-h-96 overflow-y-auto divide-y divide-gray-100">
            @php
                $notifications = Auth::user()->notifications()->take(15)->get();
            @endphp

            @forelse($notifications as $notification)
                @php
                    $notificationData = $notification->data;
                    $isRead = $notification->read_at !== null;
                    $actionUrl = $notificationData['action_url'] ?? '#';
                    $status = $notificationData['status'] ?? 'info';

                    $iconClass = 'bg-blue-100';
                    $iconSvg = '<i class="fas fa-info-circle text-blue-600 text-sm"></i>';

                    if ($status === 'approved') {
                        $iconClass = 'bg-green-100';
                        $iconSvg = '<i class="fas fa-check-circle text-green-600 text-sm"></i>';
                    } elseif ($status === 'rejected') {
                        $iconClass = 'bg-red-100';
                        $iconSvg = '<i class="fas fa-times-circle text-red-600 text-sm"></i>';
                    } elseif ($status === 'issued') {
                        $iconClass = 'bg-orange-100';
                        $iconSvg = '<i class="fas fa-truck text-orange-600 text-sm"></i>';
                    } elseif ($status === 'returned') {
                        $iconClass = 'bg-purple-100';
                        $iconSvg = '<i class="fas fa-undo-alt text-purple-600 text-sm"></i>';
                    }
                @endphp
                <a href="{{ $actionUrl }}"
                   class="block px-4 py-3 hover:bg-gray-50 transition {{ $isRead ? 'bg-white' : 'bg-blue-50' }}">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-full {{ $iconClass }} flex items-center justify-center">
                                {!! $iconSvg !!}
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-800 {{ $isRead ? '' : 'font-semibold' }}">
                                {{ $notificationData['message'] ?? 'New notification' }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                            @if(isset($notificationData['requisition_number']))
                                <p class="text-xs text-gray-500 mt-1 font-mono">#{{ $notificationData['requisition_number'] }}</p>
                            @endif
                        </div>
                        @if(!$isRead)
                            <span class="w-2 h-2 rounded-full bg-blue-500 mt-2"></span>
                        @endif
                    </div>
                </a>
            @empty
                <div class="px-4 py-8 text-center text-gray-400">
                    <i class="fas fa-bell-slash text-3xl mb-2 block"></i>
                    <p class="text-sm">No notifications yet</p>
                </div>
            @endforelse
        </div>

        @if(Auth::user()->notifications()->count() > 0)
        <div class="px-4 py-2 border-t border-gray-200 text-center">
            <a href="{{ route('notifications.index') }}" class="text-xs text-blue-600 hover:underline">View all notifications</a>
        </div>
        @endif
    </div>
</div>

<script>
    // Global notification functions
    function toggleNotificationPanel() {
        const panel = document.getElementById('globalNotificationPanel');
        if (panel) {
            panel.classList.toggle('hidden');
        }
    }

    function closeNotificationPanel() {
        const panel = document.getElementById('globalNotificationPanel');
        if (panel) {
            panel.classList.add('hidden');
        }
    }

    function markAllNotificationsRead() {
        fetch('{{ route("notifications.mark-all-read") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        }).then(response => response.json()).then(data => {
            if (data.success) {
                location.reload();
            }
        }).catch(error => {
            console.error('Error marking notifications as read:', error);
        });
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        const bellButton = document.getElementById('globalNotificationDropdown');
        const panel = document.getElementById('globalNotificationPanel');

        if (bellButton) {
            bellButton.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleNotificationPanel();
            });
        }

        // Close panel when clicking outside
        document.addEventListener('click', function(e) {
            if (panel && bellButton) {
                if (!panel.contains(e.target) && !bellButton.contains(e.target)) {
                    closeNotificationPanel();
                }
            }
        });
    });
</script>
