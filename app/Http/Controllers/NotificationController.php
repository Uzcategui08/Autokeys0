<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Display a listing of the notifications.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)

    {
        // allow searching and filtering on the notifications page
        $q = $request->input('q');
        $status = $request->input('status'); // 'all'|'read'|'unread'
        $type = $request->input('type');

        $notificationsQuery = Auth::user()->notifications()->orderBy('created_at', 'desc');

        // if a specific type was provided and is not 'all', filter by it
        if ($type && $type !== 'all') {
            $notificationsQuery->where('type', $type);
        }

        if ($q) {
            // Case-insensitive search across common DBs: compare LOWER(data) to lowered query.
            $qLower = mb_strtolower($q, 'UTF-8');
            $notificationsQuery->whereRaw('LOWER(data) LIKE ?', ["%{$qLower}%"]);
        }

        if ($status === 'unread') {
            $notificationsQuery->whereNull('read_at');
        } elseif ($status === 'read') {
            $notificationsQuery->whereNotNull('read_at');
        }

        $notifications = $notificationsQuery->paginate(15)->appends($request->query());

        // aggregated counts by type (total and unread) using DB query (Postgres-safe)
        $notifiableType = \App\Models\User::class;
        $notifiableId = Auth::id();

        $counts = DB::table('notifications')
            ->selectRaw('type, count(*) as total, sum(case when read_at is null then 1 else 0 end) as unread, max(created_at) as last_created')
            ->where('notifiable_type', $notifiableType)
            ->where('notifiable_id', $notifiableId)
            ->groupBy('type')
            ->orderByDesc('last_created')
            ->get()
            ->mapWithKeys(function ($row) {
                return [$row->type => ['total' => (int) $row->total, 'unread' => (int) $row->unread]];
            })->toArray();

        return view('notifications.index', compact('notifications', 'q', 'status', 'type', 'counts'));
    }

    /**
     * Mark the specified notification as read.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'id' => $id]);
        }

        return redirect()->back()
            ->with('success', 'Notificación marcada como leída.');
    }

    /**
     * Mark all notifications as read.
     *
     * @return \Illuminate\Http\Response
     */
    public function markAllAsRead()
    {
        $toMark = Auth::user()->unreadNotifications;
        // if you want to restrict by type keep the where() condition, otherwise mark all
        $toMark->markAsRead();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()
            ->with('success', 'Todas las notificaciones han sido marcadas como leídas.');
    }

    /**
     * Get the count of unread notifications.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function unreadCount()
    {
        $count = Auth::user()->unreadNotifications()
            ->where('type', 'App\\Notifications\\LowStockNotification')
            ->count();

        return response()->json(['count' => $count]);
    }
}
