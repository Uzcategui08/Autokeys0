<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the notifications.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()
            ->where('type', 'App\\Notifications\\LowStockNotification')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('notifications.index', compact('notifications'));
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
        Auth::user()->unreadNotifications
            ->where('type', 'App\\Notifications\\LowStockNotification')
            ->markAsRead();

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
