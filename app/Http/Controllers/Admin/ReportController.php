<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Need;
use App\Models\NeedCategory;
use App\Models\Pledge;
use App\Models\Region;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $categories = NeedCategory::all();
        $regions = Region::where('is_3t', true)->orderBy('name')->get();

        $query = Pledge::with(['need.schoolProfile.province', 'need.schoolProfile.regency', 'need.category', 'contributorProfile.user']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('assistance_type')) {
            $query->whereHas('need', function ($q) use ($request) {
                $q->where('assistance_type', $request->assistance_type);
            });
        }

        if ($request->filled('category_id')) {
            $query->whereHas('need', function ($q) use ($request) {
                $q->where('need_category_id', $request->category_id);
            });
        }

        if ($request->filled('province')) {
            $query->whereHas('need.schoolProfile.province', function ($q) use ($request) {
                $q->where('name', $request->province);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Summary metrics on filtered query
        $totalPledgesCount = (clone $query)->count();
        $completedPledgesCount = (clone $query)->where('status', 'completed')->count();
        $inTransitCount = (clone $query)->where('status', 'in_delivery')->count();
        $totalItemsCommitted = (clone $query)->whereHas('need', function ($q) {
            $q->where('assistance_type', 'material');
        })->sum('quantity');

        $pledges = $query->latest()->paginate(15)->withQueryString();

        $provinces = Region::where('level', 'province')->orderBy('name')->pluck('name');

        return view('admin.reports.index', compact(
            'pledges',
            'categories',
            'regions',
            'provinces',
            'totalPledgesCount',
            'completedPledgesCount',
            'inTransitCount',
            'totalItemsCommitted'
        ));
    }

    public function exportCsv(Request $request)
    {
        $query = Pledge::with(['need.schoolProfile.province', 'need.schoolProfile.regency', 'need.category', 'contributorProfile.user']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('assistance_type')) {
            $query->whereHas('need', function ($q) use ($request) {
                $q->where('assistance_type', $request->assistance_type);
            });
        }

        if ($request->filled('category_id')) {
            $query->whereHas('need', function ($q) use ($request) {
                $q->where('need_category_id', $request->category_id);
            });
        }

        if ($request->filled('province')) {
            $query->whereHas('need.schoolProfile.province', function ($q) use ($request) {
                $q->where('name', $request->province);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $records = $query->latest()->get();

        $filename = 'laporan-sambungasa-' . date('Ymd-His') . '.csv';

        // Formula injection sanitizer
        $sanitizeCsv = function ($value) {
            if ($value === null) {
                return '';
            }
            $str = (string) $value;
            // Prevent CSV Formula Injection
            if (strlen($str) > 0 && in_array($str[0], ['=', '+', '-', '@', "\t", "\r"])) {
                return "'" . $str;
            }
            return $str;
        };

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($records, $sanitizeCsv) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for proper Excel display
            fputs($file, "\xEF\xBB\xBF");

            // CSV Header
            fputcsv($file, [
                'Kode Tracking',
                'Tanggal Komitmen',
                'Nama Donatur',
                'Email Donatur',
                'Sekolah Penerima',
                'NPSN',
                'Provinsi',
                'Kabupaten',
                'Judul Kebutuhan',
                'Kategori',
                'Jenis Bantuan',
                'Jumlah',
                'Satuan',
                'Status',
                'Perkiraan Tiba / Mulai',
            ]);

            foreach ($records as $p) {
                fputcsv($file, [
                    $sanitizeCsv($p->tracking_code),
                    $sanitizeCsv($p->created_at ? $p->created_at->format('Y-m-d H:i') : ''),
                    $sanitizeCsv($p->contributorProfile->user->name ?? 'Donatur'),
                    $sanitizeCsv($p->contributorProfile->user->email ?? ''),
                    $sanitizeCsv($p->need->schoolProfile->school_name ?? ''),
                    $sanitizeCsv($p->need->schoolProfile->npsn ?? ''),
                    $sanitizeCsv($p->need->schoolProfile->province->name ?? ($p->need->schoolProfile->regency->island ?? '')),
                    $sanitizeCsv($p->need->schoolProfile->regency->name ?? ''),
                    $sanitizeCsv($p->need->title ?? ''),
                    $sanitizeCsv($p->need->category->name ?? ''),
                    $sanitizeCsv(($p->need->assistance_type ?? 'material') === 'non_material' ? 'Tenaga / Relawan' : 'Barang / Material'),
                    $sanitizeCsv($p->quantity),
                    $sanitizeCsv($p->need->unit ?? ''),
                    $sanitizeCsv($p->status_label ?? $p->status),
                    $sanitizeCsv($p->estimated_delivery_at ? $p->estimated_delivery_at->format('Y-m-d') : '-'),
                ]);
            }

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
