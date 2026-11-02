<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AsetMes;
use Illuminate\Http\Request;

class AsetMesController extends Controller
{
    public function index(Request $request)
    {
        $showAllPutra = $request->boolean('show_all_putra');
        $showAllPutri = $request->boolean('show_all_putri');
        $searchPutra = trim((string) $request->input('search_putra', ''));
        $searchPutri = trim((string) $request->input('search_putri', ''));
        $statusPutra = $request->input('status_putra', '');
        $statusPutri = $request->input('status_putri', '');

        $applyFilters = function ($q, $search, $status) {
            if ($status === '1') {
                $q->where('is_active', true);
            } elseif ($status === '0') {
                $q->where('is_active', false);
            }
            if ($search !== '') {
                $q->where(function ($qq) use ($search) {
                    $qq->where('nama_aset', 'like', "%{$search}%")
                        ->orWhere('pic', 'like', "%{$search}%")
                        ->orWhereHas('penanggungJawab', function ($j) use ($search) {
                            $j->where('name', 'like', "%{$search}%");
                        });
                });
            }
        };

        $queryPutra = AsetMes::with('penanggungJawab')->where('kategori', 'putra');
        $queryPutri = AsetMes::with('penanggungJawab')->where('kategori', 'putri');
        $applyFilters($queryPutra, $searchPutra, $statusPutra);
        $applyFilters($queryPutri, $searchPutri, $statusPutri);

        $countPutra = (clone $queryPutra)->count();
        $countPutri = (clone $queryPutri)->count();

        $assetsPutra = (clone $queryPutra)
            ->orderBy('created_at', 'desc')
            ->paginate($showAllPutra ? max($countPutra, 1) : 10, ['*'], 'page_putra')
            ->withQueryString();

        $assetsPutri = (clone $queryPutri)
            ->orderBy('created_at', 'desc')
            ->paginate($showAllPutri ? max($countPutri, 1) : 10, ['*'], 'page_putri')
            ->withQueryString();

        $stats = [
            'total' => AsetMes::count(),
            'aktif' => AsetMes::where('is_active', true)->count(),
            'nonaktif' => AsetMes::where('is_active', false)->count(),
            'putra' => $countPutra,
            'putri' => $countPutri,
        ];

        $alertItems = AsetMes::where('is_active', false)->orderBy('created_at', 'desc')->get();

        $alertJson = $alertItems->map(fn ($a) => [
            'id' => $a->id,
            'nama_aset' => $a->nama_aset,
            'kategori' => $a->kategori,
            'pic' => $a->pic,
            'is_active' => $a->is_active,
        ]);

        $assetsJson = AsetMes::with('penanggungJawab')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($a) => [
                'id' => $a->id,
                'nama_aset' => $a->nama_aset,
                'kategori' => $a->kategori,
                'jumlah' => $a->jumlah,
                'penanggung_jawab' => $a->penanggung_jawab,
                'penanggung_jawab_nama' => $a->penanggungJawab?->name ?? '-',
                'pic' => $a->pic,
                'jabatan' => $a->jabatan,
                'keterangan' => $a->keterangan,
                'is_active' => $a->is_active,
            ])
            ->values();

        $additionalCategories = [];
        foreach (['koordinator', 'johen_streamer'] as $kategori) {
            $search = trim((string) $request->input('search_'.$kategori, ''));
            $query = AsetMes::with('penanggungJawab')->where('kategori', $kategori);
            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_aset', 'like', "%{$search}%")
                        ->orWhere('pic', 'like', "%{$search}%");
                });
            }
            $additionalCategories[$kategori] = [
                'label' => AsetMes::KATEGORI_LABELS[$kategori],
                'search' => $search,
                'assets' => $query->orderByDesc('created_at')->paginate(10, ['*'], 'page_'.$kategori)->withQueryString(),
            ];
        }

        return view('admin.aset-mes.index', [
            'assetsPutra' => $assetsPutra,
            'assetsPutri' => $assetsPutri,
            'assetsJson' => $assetsJson,
            'additionalCategories' => $additionalCategories,
            'stats' => $stats,
            'alertItems' => $alertItems,
            'alertJson' => $alertJson,
            'penanggungJawabMes' => AsetMes::PENANGGUNG_JAWAB_MES,
            'showAllPutra' => $showAllPutra,
            'showAllPutri' => $showAllPutri,
            'searchPutra' => $searchPutra,
            'searchPutri' => $searchPutri,
            'statusPutra' => $statusPutra,
            'statusPutri' => $statusPutri,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_aset' => 'required|string|max:255',
            'kategori' => 'required|in:putra,putri,koordinator,johen_streamer',
            'jumlah' => 'nullable|integer|min:1',
            'penanggung_jawab' => 'nullable|exists:users,id',
            'pic' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $data['is_active'] = true;

        $asset = AsetMes::create($data);

        return redirect()->route('admin.aset-mes.index')->with('success', 'Aset MES berhasil ditambahkan.');
    }

    public function update(Request $request, AsetMes $asetMes)
    {
        $rules = [
            'nama_aset' => 'sometimes|required|string|max:255',
            'kategori' => 'sometimes|in:putra,putri,koordinator,johen_streamer',
            'jumlah' => 'nullable|integer|min:1',
            'penanggung_jawab' => 'nullable|exists:users,id',
            'pic' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ];

        $data = $request->validate($rules);
        $asetMes->update($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'keterangan' => $asetMes->fresh()->keterangan]);
        }

        return redirect()->route('admin.aset-mes.index')->with('success', 'Aset MES berhasil diperbarui.');
    }

    public function destroy(AsetMes $asetMes)
    {
        $asetMes->delete();

        return redirect()->route('admin.aset-mes.index')->with('success', 'Aset MES berhasil dihapus.');
    }
}
