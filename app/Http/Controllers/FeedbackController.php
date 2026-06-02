<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    // ============================================================
    //  ADMIN – Danh sách phản hồi
    // ============================================================

    public function index(Request $request)
    {
        $query = Feedback::with('user')
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), fn($q) =>
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('identity_card', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%')
            )
            ->latest();

        $feedbacks      = $query->paginate(15)->withQueryString();
        $pendingCount   = Feedback::where('status', 'pending')->count();
        $processingCount= Feedback::where('status', 'processing')->count();
        $resolvedCount  = Feedback::where('status', 'resolved')->count();

        return view('feedbacks.index', compact(
            'feedbacks', 'pendingCount', 'processingCount', 'resolvedCount'
        ));
    }

    // ============================================================
    //  ADMIN – Xem chi tiết + cập nhật ghi chú / trạng thái
    // ============================================================

    public function update(Request $request, Feedback $feedback)
    {
        $validated = $request->validate([
            'status'     => ['required', 'in:pending,processing,resolved'],
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $feedback->update($validated);

        return back()->with('success', '✅ Đã cập nhật phản hồi thành công.');
    }

}
