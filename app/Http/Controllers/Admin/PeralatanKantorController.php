<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PeralatanKantorTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\PeralatanKantorImport;
use App\Models\PeralatanKantor;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class PeralatanKantorController extends Controller
{
    public function index(Request $request)
    {
        $showAll = $request->boolean('show_all');
        $activeTim = $request->input('tim');
        $search = trim((string) $request->input('search', ''));
        $kondisi = $request->input('kondisi', '');
        $range = in_array($request->input('range'), ['harian', 'mingguan', 'bulanan'], true)
            ? $request->input('range')
            : '';

        $query = PeralatanKantor::query()->ofTim($activeTim);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                    ->orWhere('kode_aset', 'like', "%{$search}%")
                    ->orWhere('pic', 'like', "%{$search}%")
                    ->orWhere('lokasi_unit', 'like', "%{$search}%");
            });
        }
        if ($kondisi && $kondisi !== 'all') {
            $query->where('kondisi', $kondisi);
        }
        if (in_array($range, ['harian', 'mingguan', 'bulanan'], true)) {
            $start = match ($range) {
                'harian' => now()->startOfDay(),
                'mingguan' => now()->startOfWeek(),
                'bulanan' => now()->startOfMonth(),
            };
            $end = match ($range) {
                'harian' => now()->endOfDay(),
                'mingguan' => now()->endOfWeek(),
                'bulanan' => now()->endOfMonth(),
            };
            $query->whereBetween('created_at', [$start, $end]);
        }

        $allItems = (clone $query)->orderBy('created_at', 'desc')->get();

        $stats = [
            'total' => $allItems->count(),
            'kondisi_baik' => $allItems->where('kondisi', 'baik')->count(),
            'perlu_servis' => $allItems->where('kondisi', 'perlu_servis')->count(),
            'rusak' => $allItems->where('kondisi', 'rusak')->count(),
            'kondisi_lainnya' => $allItems
                ->whereNotIn('kondisi', ['baik', 'perlu_servis', 'rusak'])
                ->whereNotNull('kondisi')
                ->where('kondisi', '!=', '')
                ->count(),
            'total_nilai' => $allItems->sum('nilai'),
            'total_harga_sekarang' => $allItems->sum(function ($i) {
                $masaBarang = max($i->estimasi_waktu_barang ?: 360, 1);
                $waktuPakai = max((int) $i->waktu_pakai_per_hari, 1);
                $pengurangan = ($i->nilai / $masaBarang) * $waktuPakai;

                return max($i->nilai - $pengurangan, 0);
            }),
        ];

        $kondisiOptions = $allItems
            ->pluck('kondisi')
            ->filter(fn ($v) => $v !== null && $v !== '')
            ->unique()
            ->sort()
            ->values();

        $alertItems = $allItems->whereIn('kondisi', ['perlu_servis', 'rusak'])->values();

        $alertJson = $alertItems->map(fn ($i) => [
            'id' => $i->id,
            'nama_barang' => $i->nama_barang,
            'kode_aset' => $i->kode_aset,
            'lokasi_unit' => $i->lokasi_unit,
            'kondisi' => $i->kondisi,
        ]);

        $items = (clone $query)->orderBy('created_at', 'desc')->paginate($showAll ? max($allItems->count(), 1) : 10)->withQueryString();

        $itemsJson = $allItems->values()->map(fn ($i) => $this->serializeItem($i));

        $allTim = Team::where('is_active', true)
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->orderBy('name')
            ->pluck('name')
            ->merge(PeralatanKantor::whereNotNull('tim')->where('tim', '!=', '')->distinct()->pluck('tim'))
            ->unique()
            ->sort()
            ->values();

        $timKoordinators = Team::with('leader:id,name,team_id,role')
            ->get()
            ->filter(fn ($team) => $team->leader)
            ->mapWithKeys(fn ($team) => [$team->name => ['pic' => $team->leader->name]])
            ->toArray();

        return view('admin.peralatan-kantor.index', [
            'items' => $items,
            'itemsJson' => $itemsJson,
            'stats' => $stats,
            'alertItems' => $alertItems,
            'alertJson' => $alertJson,
            'allTim' => $allTim,
            'activeTim' => $activeTim,
            'timKoordinators' => $timKoordinators,
            'kondisiOptions' => $kondisiOptions,
            'showAll' => $showAll,
            'search' => $search,
            'kondisi' => $kondisi,
            'range' => $range,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_aset' => 'nullable|string|max:255|unique:peralatan_kantor,kode_aset',
            'barcode' => 'nullable|string|max:255|unique:peralatan_kantor,barcode',
            'foto' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'nama_barang' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'detail' => 'nullable|string',
            'sub_kategori' => 'required|string|max:255',
            'tim' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'lokasi_unit' => 'required|string|max:255',
            'ruangan' => 'required|string|max:255',
            'milik' => 'required|string|max:255',
            'pengadaan_tahun' => 'required|integer|min:1900|max:'.(now()->year + 1),
            'tanggal_pembelian' => 'required|date',
            'kategori_nilai' => 'required|string|max:255',
            'kategori_ukuran' => 'required|string|max:255',
            'nilai' => 'required|numeric|min:0',
            'waktu_pakai_per_hari' => 'required|integer|min:1',
            'estimasi_waktu_barang' => 'required|integer|min:0',
            'pic' => 'required|string|max:255',
            'jabatan' => 'required|in:Chief Executive Officer (CEO),General Manager (GM),Head of Store,Admin Master,HR,Koordinator,Karyawan',
            'atasan' => 'required|string|max:255',
            'jabatan_atasan' => 'required|in:Chief Executive Officer (CEO),General Manager (GM),Head of Store,Admin Master,HR,Koordinator,Karyawan',
            'kondisi' => 'required|string|max:255',
            'barcode_ditempel' => 'boolean',
        ]);

        $masaBarang = max($data['estimasi_waktu_barang'], 1);
        $waktuPakai = max((int) $data['waktu_pakai_per_hari'], 1);
        $data['waktu_pakai_per_hari'] = $waktuPakai;
        $data['barcode_ditempel'] = $request->boolean('barcode_ditempel');
        $data['pengurangan_harga_per_hari'] = ($data['nilai'] / $masaBarang) * $waktuPakai;
        $data['harga_per_hari_ini'] = max($data['nilai'] - $data['pengurangan_harga_per_hari'], 0);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('peralatan-kantor', 'public_storage');
        }
        $data['foto'] = $fotoPath;

        $item = PeralatanKantor::create($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Peralatan kantor berhasil ditambahkan.', 'data' => $item], 201);
        }

        return redirect()->route('admin.peralatan-kantor.index')->with('success', 'Peralatan kantor berhasil ditambahkan.');
    }

    public function update(Request $request, PeralatanKantor $peralatanKantor)
    {
        $data = $request->validate([
            'kode_aset' => 'nullable|string|max:255|unique:peralatan_kantor,kode_aset,'.$peralatanKantor->id,
            'barcode' => 'nullable|string|max:255|unique:peralatan_kantor,barcode,'.$peralatanKantor->id,
            'foto' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'nama_barang' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'detail' => 'nullable|string',
            'sub_kategori' => 'required|string|max:255',
            'tim' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'lokasi_unit' => 'required|string|max:255',
            'ruangan' => 'required|string|max:255',
            'milik' => 'required|string|max:255',
            'pengadaan_tahun' => 'required|integer|min:1900|max:'.(now()->year + 1),
            'tanggal_pembelian' => 'required|date',
            'kategori_nilai' => 'required|string|max:255',
            'kategori_ukuran' => 'required|string|max:255',
            'nilai' => 'required|numeric|min:0',
            'waktu_pakai_per_hari' => 'required|integer|min:1',
            'estimasi_waktu_barang' => 'required|integer|min:0',
            'pic' => 'required|string|max:255',
            'jabatan' => 'required|in:Chief Executive Officer (CEO),General Manager (GM),Head of Store,Admin Master,HR,Koordinator,Karyawan',
            'atasan' => 'required|string|max:255',
            'jabatan_atasan' => 'required|in:Chief Executive Officer (CEO),General Manager (GM),Head of Store,Admin Master,HR,Koordinator,Karyawan',
            'kondisi' => 'required|string|max:255',
            'barcode_ditempel' => 'boolean',
        ]);

        $masaBarang = max($data['estimasi_waktu_barang'], 1);
        $waktuPakai = max((int) $data['waktu_pakai_per_hari'], 1);
        $data['waktu_pakai_per_hari'] = $waktuPakai;
        $data['barcode_ditempel'] = $request->boolean('barcode_ditempel');
        $data['pengurangan_harga_per_hari'] = ($data['nilai'] / $masaBarang) * $waktuPakai;
        $data['harga_per_hari_ini'] = max($data['nilai'] - $data['pengurangan_harga_per_hari'], 0);

        if ($request->hasFile('foto')) {
            if ($peralatanKantor->foto) {
                Storage::disk('public_storage')->delete($peralatanKantor->foto);
            }
            $data['foto'] = $request->file('foto')->store('peralatan-kantor', 'public_storage');
        } else {
            unset($data['foto']);
        }

        $peralatanKantor->update($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Peralatan kantor berhasil diperbarui.']);
        }

        return redirect()->route('admin.peralatan-kantor.index')->with('success', 'Peralatan kantor berhasil diperbarui.');
    }

    public function destroy(PeralatanKantor $peralatanKantor)
    {
        if ($peralatanKantor->foto) {
            Storage::disk('public_storage')->delete($peralatanKantor->foto);
        }
        $peralatanKantor->delete();

        return redirect()->route('admin.peralatan-kantor.index')->with('success', 'Peralatan kantor berhasil dihapus.');
    }

    public function toggleBarcodeDitempel(PeralatanKantor $peralatanKantor)
    {
        $peralatanKantor->barcode_ditempel = ! $peralatanKantor->barcode_ditempel;
        $peralatanKantor->save();

        return response()->json([
            'success' => true,
            'barcode_ditempel' => (bool) $peralatanKantor->barcode_ditempel,
        ]);
    }

    public function resetData()
    {
        PeralatanKantor::query()->delete();

        return redirect()->route('admin.peralatan-kantor.index')->with('success', 'Semua data peralatan kantor berhasil dihapus. Silakan import ulang.');
    }

    public function scan(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:255',
        ]);

        $code = $request->input('code');

        if (preg_match('#/aset/([^/?&#]+)#', $code, $m)) {
            $code = urldecode($m[1]);
        }

        $item = PeralatanKantor::where('barcode', $code)
            ->orWhere('kode_aset', $code)
            ->first();

        if (! $item) {
            return response()->json(['message' => 'Data aset tidak ditemukan.'], 404);
        }

        return response()->json($this->serializeItem($item));
    }

    public function showJson(PeralatanKantor $peralatanKantor)
    {
        return response()->json($this->serializeItem($peralatanKantor));
    }

    protected function serializeItem(PeralatanKantor $i): array
    {
        $masaBarang = max($i->estimasi_waktu_barang ?: 360, 1);
        $penyusutanPerHari = $i->nilai / $masaBarang;
        $waktuPakai = max((int) $i->waktu_pakai_per_hari, 1);
        $penguranganHariIni = $penyusutanPerHari * $waktuPakai;
        $nilaiSekarang = max($i->nilai - $penguranganHariIni, 0);
        $hariTerpakai = $i->tanggal_pembelian ? max(abs(now()->diffInDays($i->tanggal_pembelian)), 0) : 0;

        return [
            'id' => $i->id,
            'kode_aset' => $i->kode_aset,
            'barcode' => $i->barcode,
            'foto' => $i->foto ? route('files.show', $i->foto) : null,
            'nama_barang' => $i->nama_barang,
            'jumlah' => $i->jumlah,
            'detail' => $i->detail,
            'sub_kategori' => $i->sub_kategori,
            'tim' => $i->tim,
            'keterangan' => $i->keterangan,
            'lokasi_unit' => $i->lokasi_unit,
            'ruangan' => $i->ruangan,
            'milik' => $i->milik,
            'pengadaan_tahun' => $i->pengadaan_tahun,
            'tanggal_pembelian' => $i->tanggal_pembelian?->format('Y-m-d'),
            'kategori_nilai' => $i->kategori_nilai,
            'kategori_ukuran' => $i->kategori_ukuran,
            'nilai' => (int) $i->nilai,
            'waktu_pakai_per_hari' => $waktuPakai,
            'estimasi_waktu_barang' => $i->estimasi_waktu_barang,
            'pengurangan_harga_per_hari' => round($penguranganHariIni, 2),
            'harga_per_hari_ini' => round($nilaiSekarang, 2),
            'hari_terpakai' => $hariTerpakai,
            'penyusutan_per_hari' => round($penguranganHariIni, 2),
            'nilai_sekarang' => round($nilaiSekarang, 2),
            'pic' => $i->pic,
            'jabatan' => $i->jabatan,
            'atasan' => $i->atasan,
            'jabatan_atasan' => $i->jabatan_atasan,
            'kondisi' => $i->kondisi,
            'barcode_ditempel' => (bool) $i->barcode_ditempel,
        ];
    }

    public function downloadTemplate()
    {
        return Excel::download(
            new PeralatanKantorTemplateExport,
            'Template_Import_Peralatan_Kantor.xlsx'
        );
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $import = new PeralatanKantorImport;
        Excel::import($import, $request->file('file'));

        $successCount = $import->getSuccessCount();
        $errors = $import->getErrors();

        $totalErrors = count($errors);

        if ($totalErrors > 0) {
            $message = "Berhasil import {$successCount} data.";
            $message .= " {$totalErrors} baris gagal.";
            session()->flash('import_errors', $errors);
            session()->flash('import_success_count', $successCount);
            session()->flash('import_error_count', $totalErrors);

            return redirect()->route('admin.peralatan-kantor.index')
                ->with('warning', $message);
        }

        return redirect()->route('admin.peralatan-kantor.index')
            ->with('success', "Berhasil import {$successCount} data peralatan kantor.");
    }
}
