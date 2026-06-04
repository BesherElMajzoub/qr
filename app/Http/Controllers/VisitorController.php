<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Visitor;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VisitorController extends Controller
{
    /**
     * Render the admin dashboard with statistics and visitor records.
     */
    public function index(Request $request): View
    {
        $visitors = Visitor::orderBy('created_at', 'desc')->get();

        $stats = [
            'total' => $visitors->count(),
            'today' => $visitors->filter(function (Visitor $v): bool {
                return $v->created_at->isToday();
            })->count(),
            'unique' => $visitors->unique('qr_raw_data')->count(),
        ];

        return view('dashboard', compact('visitors', 'stats'));
    }

    /**
     * Process scanned QR code data via AJAX.
     */
    public function scan(Request $request): JsonResponse
    {
        $request->validate([
            'qr_raw_data' => 'required|string',
        ]);

        $qrRawData = $request->input('qr_raw_data');

        // Check if QR data is valid JSON
        $decoded = json_decode($qrRawData, true);

        $name = null;
        $email = null;
        $phone = null;

        if (is_array($decoded)) {
            $name = $decoded['name'] ?? null;
            $email = $decoded['email'] ?? null;
            $phone = $decoded['phone'] ?? null;
        }

        // Check for duplicates
        $existing = Visitor::where('qr_raw_data', $qrRawData)->first();

        if ($existing) {
            return response()->json([
                'status' => 'duplicate',
                'message' => 'هذا الزائر تم مسحه وتسجيله مسبقاً!',
                'visitor' => $existing,
            ], 200);
        }

        // Save new visitor
        $visitor = Visitor::create([
            'qr_raw_data' => $qrRawData,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'scanned_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تسجيل الزائر بنجاح!',
            'visitor' => $visitor,
        ], 201);
    }

    /**
     * Stream export visitors to CSV with UTF-8 BOM for proper Arabic support in Excel.
     */
    public function export(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="visitors-' . date('Y-m-d-H-i-s') . '.csv"',
        ];

        $callback = function (): void {
            $file = fopen('php://output', 'w');

            // Add UTF-8 BOM to prevent Arabic character corruption in Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Write CSV headers
            fputcsv($file, [
                'ID',
                'QR Raw Data',
                'Name',
                'Email',
                'Phone',
                'IP Address',
                'User Agent',
                'Scanned At',
            ]);

            $visitors = Visitor::orderBy('created_at', 'desc')->get();

            foreach ($visitors as $visitor) {
                fputcsv($file, [
                    $visitor->id,
                    $visitor->qr_raw_data,
                    $visitor->name,
                    $visitor->email,
                    $visitor->phone,
                    $visitor->ip_address,
                    $visitor->user_agent,
                    $visitor->scanned_at->toDateTimeString(),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display the token login page.
     */
    public function showLogin(Request $request): View|RedirectResponse
    {
        if (session('dashboard_authenticated') === true) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Process manual security token entry.
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $expectedToken = config('app.dashboard_token');

        if ($request->input('token') === $expectedToken) {
            session(['dashboard_authenticated' => true]);

            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'token' => 'رمز الدخول الأمني غير صحيح. يرجى المحاولة مرة أخرى.',
        ]);
    }

    /**
     * Log out from the dashboard session.
     */
    public function logout(Request $request): RedirectResponse
    {
        session()->forget('dashboard_authenticated');

        return redirect()->route('login')->with('success', 'تم تسجيل الخروج بنجاح.');
    }
}
