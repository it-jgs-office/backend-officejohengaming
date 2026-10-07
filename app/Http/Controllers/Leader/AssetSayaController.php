<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\AsetMes;
use App\Models\AsetSaya;
use App\Models\AsetTim;
use App\Models\DigitalAsset;
use App\Models\PeralatanKantor;
use App\Models\SimCard;
use App\Models\SosialMedia;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AssetSayaController extends Controller
{
    private const KATEGORI_MAP = [
        'Data Aset Saya' => AsetSaya::class,
        'Kendaraan' => Vehicle::class,
        'Digital' => DigitalAsset::class,
        'Sosial Media' => SosialMedia::class,
        'SIM Card' => SimCard::class,
        'Peralatan Kantor' => PeralatanKantor::class,
        'Aset MES' => AsetMes::class,
        'Aset TIM' => AsetTim::class,
    ];

    public function index(Request $request)
    {
        $user = auth()->user();
        $userName = $user->name;
        $userIdentifiers = array_values(array_unique(array_filter([$user->name, $user->username])));
        $userId = auth()->id();

        $assets = $this->getMyAssets($userName, $userId, $userIdentifiers);

        if ($search = $request->input('search')) {
            $assets = $assets->filter(fn ($a) => str_contains(strtolower($a['nama_aset']), strtolower($search))
                || str_contains(strtolower($a['kode_aset'] ?? ''), strtolower($search))
                || str_contains(strtolower($a['pic']), strtolower($search))
                || str_contains(strtolower($a['kategori']), strtolower($search))
            );
        }

        if ($kategori = $request->input('kategori')) {
            $assets = $assets->filter(fn ($a) => $a['kategori'] === $kategori);
        }

        $sortField = $request->input('sort', 'created_at');
        $sortDir = $request->input('dir', 'desc');
        $assets = $assets->sortBy($sortField, SORT_REGULAR, $sortDir === 'asc')->values();

        $perPage = 10;
        $page = $request->input('page', 1);
        $total = $assets->count();
        $items = $assets->slice(($page - 1) * $perPage, $perPage);
        $paginator = new LengthAwarePaginator($items, $total, $perPage, $page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        $kategoriCounts = $assets->groupBy('kategori')->map(fn ($g) => $g->count());
        $allKategoris = array_keys(self::KATEGORI_MAP);

        $asetSaya = AsetSaya::where('penanggung_jawab', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('leader.asset-saya.index', compact('paginator', 'assets', 'kategoriCounts', 'allKategoris', 'asetSaya'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_aset' => 'required|string|max:255',
            'jenis_aset' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'daya' => 'nullable|string|max:255',
        ]);

        $data['penanggung_jawab'] = auth()->id();
        $data['is_active'] = true;

        AsetSaya::create($data);

        return redirect()->route('koordinator.asset-saya.index')->with('success', 'Aset berhasil ditambahkan.');
    }

    public function update(Request $request, string $kategori, int $id)
    {
        $modelClass = self::KATEGORI_MAP[$kategori] ?? null;
        if (! $modelClass) {
            abort(404);
        }

        $model = $modelClass::findOrFail($id);

        $rules = match ($kategori) {
            'Data Aset Saya' => [
                'nama_aset' => 'sometimes|required|string|max:255',
                'jenis_aset' => 'nullable|string|max:255',
                'keterangan' => 'nullable|string',
                'daya' => 'nullable|string|max:255',
                'unit' => 'nullable|string|max:255',
                'is_active' => 'sometimes|boolean',
            ],
            'Kendaraan' => [
                'nama_kendaraan' => 'sometimes|required|string|max:255',
                'plat_nomor' => 'nullable|string|max:255',
                'jenis_kendaraan' => 'nullable|string|max:255',
                'merk_tipe' => 'nullable|string|max:255',
                'warna' => 'nullable|string|max:255',
                'tahun' => 'nullable|integer',
                'keperluan' => 'nullable|string|max:255',
            ],
            'Digital' => [
                'nama_aset' => 'sometimes|required|string|max:255',
                'email' => 'nullable|email|max:255',
                'is_active' => 'sometimes|boolean',
                'keperluan' => 'nullable|string|max:255',
            ],
            'Sosial Media' => [
                'nama' => 'sometimes|required|string|max:255',
                'username' => 'nullable|string|max:255',
                'platform' => 'nullable|string|max:255',
                'status' => 'nullable|string|max:255',
                'ket' => 'nullable|string',
            ],
            'SIM Card' => [
                'nomor_sim_card' => 'sometimes|required|string|max:255',
                'status_kartu' => 'sometimes|boolean',
                'keperluan' => 'nullable|string|max:255',
            ],
            'Peralatan Kantor' => [
                'nama_barang' => 'sometimes|required|string|max:255',
                'kondisi' => 'nullable|string|max:255',
                'lokasi_unit' => 'nullable|string|max:255',
                'keterangan' => 'nullable|string',
            ],
            'Aset MES' => [
                'nama_aset' => 'sometimes|required|string|max:255',
                'is_active' => 'sometimes|boolean',
            ],
            'Aset TIM' => [
                'nama_aset' => 'sometimes|required|string|max:255',
                'is_active' => 'sometimes|boolean',
            ],
            default => [],
        };

        $data = $request->validate($rules);
        $model->update($data);

        return redirect()->route('koordinator.asset-saya.index')->with('success', 'Aset berhasil diperbarui.');
    }

    public function destroy(string $kategori, int $id)
    {
        $modelClass = self::KATEGORI_MAP[$kategori] ?? null;
        if (! $modelClass) {
            abort(404);
        }

        $model = $modelClass::findOrFail($id);
        $model->delete();

        return redirect()->route('koordinator.asset-saya.index')->with('success', 'Aset berhasil dihapus.');
    }

    private function getMyAssets(string $userName, int $userId, array $userIdentifiers): Collection
    {
        $assets = collect();

        $assets = $assets->merge(
            AsetSaya::where('penanggung_jawab', $userId)->get()->map(fn ($a) => $this->mapItem($a, 'Data Aset Saya', $a->nama_aset, '-', '-', $a->penanggungJawab?->name ?? '-', $a->jabatan ?? '-', null, $a->created_at, $a->is_active ? 'Aktif' : 'Tidak Aktif'))
        );

        $assets = $assets->merge(
            Vehicle::where('pic', $userName)->get()->map(fn ($v) => $this->mapItem($v, 'Kendaraan', $v->nama_kendaraan, $v->plat_nomor, '-', $v->pic, $v->jabatan, null, $v->created_at, ucfirst(str_replace('_', ' ', $v->status_pajak))))
        );

        $assets = $assets->merge(
            DigitalAsset::where('pic', $userName)->get()->map(fn ($d) => $this->mapItem($d, 'Digital', $d->nama_aset, $d->email, '-', $d->pic, $d->jabatan, null, $d->created_at, $d->is_active ? 'Aktif' : 'Tidak Aktif'))
        );

        $assets = $assets->merge(
            SosialMedia::where(function ($query) use ($userIdentifiers) {
                foreach ($userIdentifiers as $identifier) {
                    $query->orWhereRaw('LOWER(TRIM(pic)) = ?', [mb_strtolower(trim($identifier))]);
                }
            })->get()->map(fn ($s) => $this->mapItem($s, 'Sosial Media', $s->nama, $s->username, $s->platform, $s->pic, '-', null, $s->created_at, $s->status === 'aktif' ? 'Aktif' : 'Nonaktif'))
        );

        $assets = $assets->merge(
            SimCard::where('pic', $userName)->get()->map(fn ($s) => $this->mapItem($s, 'SIM Card', $s->nomor_sim_card, $s->nomor_sim_card, '-', $s->pic, $s->jabatan, $s->atasan, $s->created_at, $s->status_kartu ? 'Aktif' : 'Nonaktif'))
        );

        $assets = $assets->merge(
            PeralatanKantor::where('pic', $userName)->get()->map(fn ($p) => $this->mapItem($p, 'Peralatan Kantor', $p->nama_barang, $p->kode_aset ?? '-', $p->lokasi_unit, $p->pic ?? '-', $p->jabatan ?? '-', $p->atasan ?? '-', $p->created_at, ucfirst($p->kondisi)))
        );

        $assets = $assets->merge(
            AsetMes::where('penanggung_jawab', $userId)->get()->map(fn ($m) => $this->mapItem($m, 'Aset MES', $m->nama_aset, '-', '-', $m->penanggungJawab?->name ?? ($m->pic ?? '-'), $m->jabatan ?? '-', null, $m->created_at, $m->is_active ? 'Aktif' : 'Tidak Aktif'))
        );

        $assets = $assets->merge(
            AsetTim::where('penanggung_jawab', $userId)->get()->map(fn ($t) => $this->mapItem($t, 'Aset TIM', $t->nama_aset, '-', $t->tim ?? '-', $t->penanggungJawab?->name ?? ($t->pic ?? '-'), $t->jabatan ?? '-', null, $t->created_at, $t->is_active ? 'Aktif' : 'Tidak Aktif'))
        );

        return $assets;
    }

    private function mapItem($model, string $kategori, string $namaAset, string $kodeAset, string $lokasi, string $pic, string $jabatan, ?string $atasan, $createdAt, string $status): array
    {
        return [
            'id' => $model->id,
            'kategori' => $kategori,
            'nama_aset' => $namaAset,
            'kode_aset' => $kodeAset,
            'lokasi' => $lokasi,
            'pic' => $pic,
            'jabatan' => $jabatan,
            'atasan' => $atasan ?? '-',
            'created_at' => $createdAt,
            'status' => $status,
        ];
    }
}
