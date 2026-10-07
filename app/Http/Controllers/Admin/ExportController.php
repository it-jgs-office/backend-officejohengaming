<?php

namespace App\Http\Controllers\Admin;

use App\Exports\DataExport;
use App\Http\Controllers\Controller;
use App\Models\AsetMes;
use App\Models\AsetRuko;
use App\Models\AsetTim;
use App\Models\Asset;
use App\Models\DigitalAsset;
use App\Models\ElectricityTokenReading;
use App\Models\InternetQuotaReading;
use App\Models\InternetQuotaTopup;
use App\Models\InternetUsageCheck;
use App\Models\Meeting;
use App\Models\PembayaranAsetDigital;
use App\Models\PembayaranIplRuko;
use App\Models\PeralatanKantor;
use App\Models\Room;
use App\Models\SimCard;
use App\Models\SosialMedia;
use App\Models\Team;
use App\Models\TokenPayment;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\WifiPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function export(Request $request)
    {
        $type = $request->query('type');
        $jenis = $request->query('jenis');
        $filter = $request->query('filter', 'all');

        if ($type === 'peralatan-kantor' && $request->query('range') === 'mingguan') {
            $request->validate([
                'week_start' => ['required', 'date'],
                'week_end' => ['required', 'date', 'after_or_equal:week_start'],
            ]);
        }

        $exports = [
            'assets' => fn () => $this->assetsExport($filter),
            'users' => fn () => $this->usersExport($filter),
            'admins' => fn () => $this->adminsExport($filter),
            'teams' => fn () => $this->teamsExport($filter),
            'rooms' => fn () => $this->roomsExport($filter),
            'meetings' => fn () => $this->meetingsExport($request),
            'vehicles' => fn () => $this->vehiclesExport($filter),
            'digital-assets' => fn () => $this->digitalAssetsExport($filter),
            'sim-cards' => fn () => $this->simCardsExport($filter),
            'sosial-media' => fn () => $this->sosialMediaExport($filter),
            'peralatan-kantor' => fn () => $this->peralatanKantorExport($filter, $request->query('tim'), $request->query('range'), $request->query('date'), $request->query('week_start'), $request->query('week_end'), $request->query('month')),
            'aset-tim' => fn () => $this->asetTimExport($request),
            'aset-mes' => fn () => $this->asetMesExport($filter),
            'ruko' => fn () => $this->rukoExport($filter),
            'pembayaran' => fn () => $this->pembayaranExport($jenis, $filter),
            'token-readings' => fn () => $this->tokenReadingsExport($request),
            'token-topups' => fn () => $this->tokenTopupsExport($request),
            'internet-usage' => fn () => $this->internetUsageExport($request),
            'internet-quota-topups' => fn () => $this->internetQuotaTopupsExport($request),
            'internet-quota-readings' => fn () => $this->internetQuotaReadingsExport($request),
        ];

        if (! isset($exports[$type])) {
            return redirect()->back()->with('error', 'Tipe export tidak valid.');
        }

        return $exports[$type]();
    }

    protected function assetsExport($filter = 'all')
    {
        $data = Asset::orderBy('created_at', 'desc')->get()->map(fn ($a) => [
            'Nama Aset' => $a->nama_aset,
            'Kategori' => $a->kategori,
            'Lokasi' => $a->lokasi,
            'Jumlah' => $a->jumlah,
            'Kondisi' => $a->kondisi,
            'Keterangan' => $a->keterangan ?? '-',
        ]);

        return Excel::download(
            new DataExport(collect($data), array_keys($data->first() ?? []), 'Data Aset', 'Aset'),
            'Data_Aset.xlsx'
        );
    }

    protected function usersExport($filter = 'all')
    {
        $data = User::where('role', 'user')->orderBy('name')->get()->map(fn ($u) => [
            'Nama' => $u->name,
            'Email' => $u->email,
            'Username' => $u->username,
            'Role' => $u->role,
            'Aktif' => $u->is_active ? 'Ya' : 'Tidak',
        ]);

        return Excel::download(
            new DataExport(collect($data), array_keys($data->first() ?? []), 'Data Akun Karyawan', 'Karyawan'),
            'Data_Karyawan.xlsx'
        );
    }

    protected function adminsExport($filter = 'all')
    {
        $data = User::whereIn('role', ['admin', 'head_of_store', 'gm', 'hr', 'ceo', 'assistant_manager'])
            ->orderBy('name')->get()->map(fn ($u) => [
                'Nama' => $u->name,
                'Email' => $u->email,
                'Username' => $u->username,
                'Role' => $u->role,
                'Aktif' => $u->is_active ? 'Ya' : 'Tidak',
            ]);

        return Excel::download(
            new DataExport(collect($data), array_keys($data->first() ?? []), 'Data Akun Admin', 'Admin'),
            'Data_Admin.xlsx'
        );
    }

    protected function teamsExport($filter = 'all')
    {
        $data = Team::orderBy('name')->get()->map(fn ($t) => [
            'Nama Tim' => $t->name,
            'Deskripsi' => $t->description ?? '-',
            'Dibuat' => $t->created_at->format('d/m/Y'),
        ]);

        return Excel::download(
            new DataExport(collect($data), array_keys($data->first() ?? []), 'Data Tim', 'Tim'),
            'Data_Tim.xlsx'
        );
    }

    protected function roomsExport($filter = 'all')
    {
        $data = Room::orderBy('name')->get()->map(fn ($r) => [
            'Nama Ruangan' => $r->name,
            'Lokasi' => $r->location ?? '-',
            'Kapasitas' => $r->capacity,
            'Khusus Weekly Meeting' => $r->is_weekly_only ? 'Ya' : 'Tidak',
            'Deskripsi' => $r->description ?? '-',
        ]);

        return Excel::download(
            new DataExport(collect($data), array_keys($data->first() ?? []), 'Data Ruangan', 'Ruangan'),
            'Data_Ruangan.xlsx'
        );
    }

    protected function meetingsExport($request)
    {
        $meetingMonth = $request->get('meeting_month', now()->format('Y-m'));
        $startDate = Carbon::parse($meetingMonth.'-01')->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $query = Meeting::with(['requester', 'room', 'team'])
            ->whereBetween('meeting_date', [$startDate, $endDate]);

        $statusLabelMap = [
            'pending' => 'Menunggu Review', 'approved' => 'Disetujui', 'rejected' => 'Ditolak',
            'confirmed' => 'Dikonfirmasi', 'cancelled' => 'Dibatalkan',
            'in_progress' => 'Berlangsung', 'completed' => 'Selesai',
        ];

        $data = $query->orderBy('meeting_date', 'desc')->get()->values()->map(fn ($m, $i) => [
            'No' => $i + 1,
            'Judul' => $m->title,
            'Tanggal' => $m->meeting_date->format('d/m/Y'),
            'Waktu' => substr($m->start_time, 0, 5).' - '.substr($m->end_time, 0, 5),
            'Ruangan' => $m->room?->name ?? '-',
            'Pemohon' => $m->requester?->name ?? '-',
            'Tim' => $m->team?->name ?? '-',
            'Status' => $statusLabelMap[$m->status] ?? $m->status,
            'Antrian' => $m->queue_position ? 'Antrian ke-'.$m->queue_position : ($m->status === 'completed' ? 'Selesai' : ($m->status === 'in_progress' ? 'Berlangsung' : '-')),
        ]);

        return Excel::download(
            new DataExport(collect($data), array_keys($data->first() ?? []), 'Data Meeting', 'Meeting'),
            'Data_Meeting.xlsx'
        );
    }

    protected function vehiclesExport($filter = 'all')
    {
        $query = Vehicle::orderBy('nama_kendaraan');
        if ($filter !== 'all') {
            $query->where('status_pajak', $filter);
        }
        $vehicles = $query->get()->map(fn ($v) => [
            'Nama Kendaraan' => $v->nama_kendaraan,
            'Nomor Polisi' => $v->plat_nomor ?? '-',
            'Jenis Kendaraan' => $v->jenis_kendaraan,
            'Merk / Tipe' => $v->merk_tipe ?? '-',
            'Tahun' => $v->tahun,
            'Warna' => $v->warna ?? '-',
            'Nomor Rangka' => $v->nomor_rangka ?? '-',
            'Nomor Mesin' => $v->nomor_mesin ?? '-',
            'Status Kepemilikan' => $v->kepemilikan_status,
            'Foto' => $v->foto ? route('files.show', $v->foto) : '-',
            'Keterangan' => $v->keperluan ?? '-',
            'Sumber' => 'Kendaraan',
        ]);

        $peralatan = collect();
        if ($filter === 'all') {
            $peralatan = PeralatanKantor::where('sub_kategori', 'Kendaraan')
                ->orderBy('kode_aset')
                ->get()
                ->map(fn ($p) => [
                    'Nama Kendaraan' => $p->nama_barang,
                    'Nomor Polisi' => '-',
                    'Jenis Kendaraan' => 'Motor',
                    'Merk / Tipe' => $p->detail ?? '-',
                    'Tahun' => $p->pengadaan_tahun,
                    'Warna' => '-',
                    'Nomor Rangka' => '-',
                    'Nomor Mesin' => '-',
                    'Status Kepemilikan' => $p->milik,
                    'Foto' => $p->foto ? route('files.show', $p->foto) : '-',
                    'Keterangan' => collect([
                        $p->kode_aset ? 'Kode Aset: '.$p->kode_aset : null,
                        $p->lokasi_unit ? 'Lokasi: '.$p->lokasi_unit : null,
                        $p->ruangan ? 'Ruang: '.$p->ruangan : null,
                        $p->kondisi ? 'Kondisi: '.$p->kondisi : null,
                        $p->keterangan ?: null,
                    ])->filter()->implode(' • '),
                    'Sumber' => 'Peralatan Kantor',
                ]);
        }

        $data = $vehicles->concat($peralatan);

        return Excel::download(
            new DataExport(collect($data), array_keys($data->first() ?? []), 'Data Kendaraan', 'Kendaraan'),
            'Data_Kendaraan.xlsx'
        );
    }

    protected function digitalAssetsExport($filter = 'all')
    {
        $query = DigitalAsset::orderBy('nama_aset');
        if ($filter !== 'all') {
            $query->where('is_active', $filter === 'aktif' ? 1 : 0);
        }
        $data = $query->get()->map(fn ($a) => [
            'Nama Aset' => $a->nama_aset,
            'Email' => $a->email,
            'Mulai' => $a->mulai?->format('d/m/Y'),
            'Berakhir' => $a->berakhir?->format('d/m/Y'),
            'Biaya' => $a->biaya ? 'Rp '.number_format($a->biaya, 0, ',', '.') : '-',
            'Status' => $a->is_active ? 'Aktif' : 'Tidak Aktif',
            'PIC' => $a->pic,
            'Jabatan' => $a->jabatan,
            'Keterangan' => $a->keperluan ?? '-',
        ]);

        return Excel::download(
            new DataExport(collect($data), array_keys($data->first() ?? []), 'Data Aset Digital', 'Aset Digital'),
            'Data_Aset_Digital.xlsx'
        );
    }

    protected function simCardsExport($filter = 'all')
    {
        $query = SimCard::orderBy('nomor_sim_card');
        if ($filter !== 'all') {
            $query->where('status_kartu', $filter === 'aktif' ? 1 : 0);
        }
        $data = $query->get()->map(fn ($s) => [
            'Nomor SIM Card' => $s->nomor_sim_card,
            'PIC' => $s->pic,
            'Atasan' => $s->atasan ?? '-',
            'Jabatan' => $s->jabatan,
            'Masa Aktif' => $s->masa_aktif?->format('d/m/Y'),
            'Masa Tenggang' => $s->masa_tenggang?->format('d/m/Y'),
            'Status Paket Kuota' => $s->status_paket_kuota ? 'Aktif' : 'Nonaktif',
            'Status Kartu' => $s->status_kartu ? 'Aktif' : 'Nonaktif',
            'Menggunakan WA' => $s->menggunakan_wa ? 'Ya' : 'Tidak',
            'Status WA' => $s->menggunakan_wa && $s->status_wa
                ? match ($s->status_wa) {
                    'aktif' => 'Aktif',
                    'banned_sementara' => 'Terbanned Sementara',
                    'banned_selamanya' => 'Terbanned Selamanya',
                    default => $s->status_wa,
                }
                : '-',
            'Keperluan' => $s->keperluan ?? '-',
        ]);

        return Excel::download(
            new DataExport(collect($data), array_keys($data->first() ?? []), 'Data SIM Card', 'SIM Card'),
            'Data_SIM_Card.xlsx'
        );
    }

    protected function sosialMediaExport($filter = 'all')
    {
        $query = SosialMedia::orderBy('username');
        if ($filter !== 'all') {
            $query->where('status', $filter);
        }
        $data = $query->get()->map(fn ($i) => [
            'Username' => $i->username,
            'Nama' => $i->nama,
            'Followers' => $i->followers ?? '-',
            'Platform' => $i->platform,
            'Status' => $i->status === 'aktif' ? 'Aktif' : 'Nonaktif',
            'Divisi' => $i->divisi,
            'PIC' => $i->pic,
        ]);

        return Excel::download(
            new DataExport(collect($data), array_keys($data->first() ?? []), 'Data Sosial Media', 'Sosial Media'),
            'Data_Sosial_Media.xlsx'
        );
    }

    protected function peralatanKantorExport($filter = 'all', ?string $tim = null, ?string $range = null, ?string $date = null, ?string $weekStart = null, ?string $weekEnd = null, ?string $month = null)
    {
        $query = PeralatanKantor::query()->ofTim($tim)->orderBy('nama_barang');
        if ($range === 'harian') {
            $selectedDate = Carbon::parse($date ?: Carbon::today()->toDateString());
            $query->whereDate('created_at', $selectedDate);
        } elseif ($range === 'mingguan') {
            $query->whereBetween('created_at', [
                Carbon::parse($weekStart)->startOfDay(),
                Carbon::parse($weekEnd)->endOfDay(),
            ]);
        } elseif ($range === 'bulanan') {
            $selectedMonth = Carbon::createFromFormat('Y-m', $month ?: Carbon::now()->format('Y-m'));
            $query->whereBetween('created_at', [$selectedMonth->copy()->startOfMonth(), $selectedMonth->copy()->endOfMonth()]);
        }
        if ($filter !== 'all') {
            $query->where('kondisi', $filter);
        }
        $data = $query->get()->map(fn ($p) => [
            'Nama Barang' => $p->nama_barang,
            'Jumlah' => $p->jumlah,
            'Detail' => $p->detail ?? '-',
            'Keterangan' => $p->keterangan ?? '-',
            'Lokasi Unit' => $p->lokasi_unit,
            'Ruangan' => $p->ruangan,
            'Pengadaan (in tahun)' => $p->pengadaan_tahun,
            'Tanggal Pembelian' => $p->tanggal_pembelian?->format('d/m/Y'),
            'Kategori Nilai' => $p->kategori_nilai,
            'Kategori Ukuran' => $p->kategori_ukuran,
            'Sub-Kategori' => $p->sub_kategori,
            'Tim' => $p->tim ?? '-',
            'Milik' => $p->milik,
            'Nilai (in Rupiah)' => $p->nilai ?? 0,
            'Waktu Pakai Barang Perhari Ini' => $p->waktu_pakai_per_hari,
            'Estimasi Waktu Barang' => $p->estimasi_waktu_barang,
            'Pengurangan Harga Aset Perhari' => $p->pengurangan_harga_per_hari,
            'Harga Barang Perhari Ini' => $p->harga_per_hari_ini,
            'PIC' => $p->pic,
            'Jabatan PIC' => $p->jabatan,
            'Atasan' => $p->atasan,
            'Jabatan Atasan' => $p->jabatan_atasan,
            'Kode Asset' => $p->kode_aset,
            'Barcode' => $p->barcode,
            'Barcode Ditempel' => $p->barcode_ditempel ? 'Sudah' : 'Belum',
        ]);

        $totals = [
            ['column' => 'N', 'label' => 'Total Nilai'],
            ['column' => 'R', 'label' => 'Total Harga Saat Ini'],
        ];

        return Excel::download(
            new DataExport(
                collect($data),
                array_keys($data->first() ?? []),
                'Data Peralatan Kantor',
                'Peralatan Kantor',
                [],
                $totals,
                'Barang dengan "Barcode Ditempel" = Sudah berarti barcode sudah ditempel pada barang.',
                [['column' => 'Barcode Ditempel', 'value' => 'Sudah', 'fill' => 'D1FAE5']]
            ),
            'Data_Peralatan_Kantor.xlsx'
        );
    }

    protected function asetTimExport($request)
    {
        $tim = $request->query('tim');
        $query = AsetTim::with('penanggungJawab')->orderBy('created_at', 'desc');
        if ($tim) {
            $query->where('tim', $tim);
        }
        $data = $query->get()->map(fn ($a) => [
            'Nama Aset' => $a->nama_aset,
            'Tim' => $a->tim ?? '-',
            'Jumlah' => $a->jumlah,
            'Penanggung Jawab' => $a->penanggungJawab?->name ?? '-',
            'PIC' => $a->pic ?? '-',
            'Jabatan' => $a->jabatan ?? '-',
            'Status' => $a->is_active ? 'Aktif' : 'Tidak Aktif',
            'Keterangan' => $a->keterangan ?? '-',
        ]);

        return Excel::download(
            new DataExport(collect($data), array_keys($data->first() ?? []), 'Data Aset TIM', 'Aset TIM'),
            'Data_Aset_TIM.xlsx'
        );
    }

    protected function asetMesExport($filter = 'all')
    {
        $query = AsetMes::with('penanggungJawab')->orderBy('nama_aset');
        if (in_array($filter, ['putra', 'putri'])) {
            $query->where('kategori', $filter);
        }
        $data = $query->get()->map(fn ($a) => [
            'Nama Aset' => $a->nama_aset,
            'Kategori' => ucfirst($a->kategori),
            'Jumlah' => $a->jumlah,
            'Penanggung Jawab' => $a->penanggungJawab?->name ?? AsetMes::PENANGGUNG_JAWAB_MES[$a->kategori] ?? '-',
            'PIC' => $a->pic ?? '-',
            'Jabatan' => $a->jabatan ?? '-',
            'Status' => $a->is_active ? 'Aktif' : 'Tidak Aktif',
            'Keterangan' => $a->keterangan ?? '-',
        ]);

        return Excel::download(
            new DataExport(collect($data), array_keys($data->first() ?? []), 'Data Aset MES', 'Aset MES'),
            'Data_Aset_MES.xlsx'
        );
    }

    protected function rukoExport($filter = 'all')
    {
        $query = AsetRuko::orderBy('nama_aset');
        if ($filter !== 'all') {
            $query->where('kondisi', $filter);
        }
        $data = $query->get()->map(fn ($r) => [
            'Nama Aset' => $r->nama_aset,
            'Lokasi' => $r->lokasi,
            'Jumlah' => $r->jumlah,
            'Kondisi' => $r->kondisi,
        ]);

        return Excel::download(
            new DataExport(collect($data), array_keys($data->first() ?? []), 'Data Aset Ruko', 'Ruko'),
            'Data_Ruko.xlsx'
        );
    }

    protected function pembayaranExport($jenis, $filter = 'all')
    {
        if ($jenis === 'internet') {
            $query = WifiPayment::orderBy('created_at', 'desc');
            if ($filter !== 'all') {
                $query->where('status', $filter);
            }
            $data = $query->get()->map(fn ($w) => [
                'Nama Internet' => $w->nama_internet,
                'Provider' => $w->provider,
                'PIC' => $w->pic,
                'Jabatan' => $w->jabatan,
                'Masa Tenggang' => $w->masa_tenggang?->format('d/m/Y'),
                'Biaya' => 'Rp '.number_format($w->biaya, 0, ',', '.'),
                'Status' => $w->status,
                'Tgl Bayar' => $w->tanggal_bayar?->format('d/m/Y') ?? '-',
            ]);

            return Excel::download(
                new DataExport(collect($data), array_keys($data->first() ?? []), 'Data Pembayaran Internet', 'Internet'),
                'Data_Pembayaran_Internet.xlsx'
            );
        }

        if ($jenis === 'aset_digital') {
            $query = PembayaranAsetDigital::orderBy('created_at', 'desc');
        } elseif ($jenis === 'ipl_ruko') {
            $query = PembayaranIplRuko::orderBy('created_at', 'desc');
        } else {
            return redirect()->back()->with('error', 'Jenis export tidak valid.');
        }
        if ($filter !== 'all') {
            $query->where('status', $filter);
        }
        $data = $query->get()->map(fn ($p) => [
            'Periode' => $p->periode,
            'Tagihan' => $p->tanggal_tagihan?->format('d/m/Y'),
            'Jatuh Tempo' => $p->jatuh_tempo?->format('d/m/Y'),
            'Nominal' => 'Rp '.number_format($p->nominal, 0, ',', '.'),
            'Status' => $p->status,
            'Tgl Bayar' => $p->tanggal_bayar?->format('d/m/Y') ?? '-',
        ]);

        $label = $jenis === 'aset_digital' ? 'Aset Digital' : ($jenis === 'ipl_ruko' ? 'IPL Ruko' : 'Tagihan');

        return Excel::download(
            new DataExport(collect($data), array_keys($data->first() ?? []), "Data Pembayaran {$label}", $label),
            "Data_Pembayaran_{$label}.xlsx"
        );
    }

    protected function tokenReadingsExport($request)
    {
        $range = $request->get('range', 'bulanan');
        $query = ElectricityTokenReading::with('checker');
        if ($range === 'harian') {
            $query->whereDate('checked_date', Carbon::today());
        } elseif ($range === 'mingguan') {
            $query->whereBetween('checked_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($range === 'tahunan') {
            $readingYear = $request->get('reading_year', now()->year);
            $query->whereYear('checked_date', $readingYear);
        } else {
            $tokenMonth = $request->get('token_month', now()->format('Y-m'));
            $startDate = Carbon::parse($tokenMonth.'-01')->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();
            $query->whereBetween('checked_date', [$startDate, $endDate]);
        }
        $latestPayment = TokenPayment::orderBy('payment_date', 'desc')->first();
        $capacityKwh = $latestPayment ? (float) $latestPayment->amount_kwh : 7000;

        $data = $query->orderBy('checked_date', 'desc')
            ->get()->map(fn ($r) => [
                'Tanggal Check' => $r->checked_date->format('d/m/Y'),
                'Sisa KWH' => $r->remaining_kwh,
                'Terpakai' => $capacityKwh - (float) $r->remaining_kwh,
                'Status' => $r->status ?? '-',
                'Pengecek' => $r->checker?->name ?? '-',
                'Catatan' => $r->notes ?: 'Tidak ada catatan',
            ]);

        return Excel::download(
            new DataExport(collect($data), array_keys($data->first() ?? []), 'Data Pengecekan Token Listrik', 'Token Listrik'),
            'Data_Token_Listrik.xlsx'
        );
    }

    protected function tokenTopupsExport($request)
    {
        $range = $request->get('range', 'bulanan');
        $query = TokenPayment::with('creator');
        if ($range === 'harian') {
            $query->whereDate('payment_date', Carbon::today());
        } elseif ($range === 'mingguan') {
            $query->whereBetween('payment_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($range === 'tahunan') {
            $topupYear = $request->get('topup_year', now()->year);
            $query->whereYear('payment_date', $topupYear);
        } else {
            $topupMonth = $request->get('topup_month', now()->format('Y-m'));
            $startDate = Carbon::parse($topupMonth.'-01')->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();
            $query->whereBetween('payment_date', [$startDate, $endDate]);
        }
        $data = $query->orderBy('payment_date', 'desc')
            ->get()->map(fn ($t) => [
                'Tanggal Bayar' => $t->payment_date->format('d/m/Y'),
                'Periode' => $t->period,
                'Jumlah KWH' => $t->amount_kwh,
                'Nominal' => $t->nominal,
                'Oleh' => $t->creator?->name ?? '-',
                'Bukti' => $t->bukti_bayar ? route('files.show', $t->bukti_bayar) : '-',
                'Catatan' => $t->notes ?: 'Tidak ada catatan',
            ]);

        return Excel::download(
            new DataExport(collect($data), array_keys($data->first() ?? []), 'Riwayat Top Up Token', 'Top Up Token'),
            'Riwayat_TopUp_Token.xlsx'
        );
    }

    protected function internetUsageExport($request)
    {
        $internetUsageDate = $request->get('internet_usage_date', now()->format('Y-m'));
        $startDate = Carbon::parse($internetUsageDate.'-01')->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $data = InternetUsageCheck::with('checker')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(fn ($u) => [
                'Ruangan' => $u->ruangan,
                'Hari' => $u->hari,
                'Tanggal' => $u->tanggal->format('d/m/Y'),
                'Penggunaan Wifi (GB)' => number_format((float) $u->penggunaan_wifi, 2),
                'Penggunaan Ethernet (GB)' => number_format((float) $u->penggunaan_ethernet, 2),
                'Pengecek' => $u->checker?->name ?? '-',
                'Keterangan' => $u->keterangan ?: 'Tidak ada catatan',
            ]);

        return Excel::download(
            new DataExport(collect($data), array_keys($data->first() ?? []), 'Data Pengecekan Usage Internet', 'Usage Internet'),
            'Data_Usage_Internet.xlsx'
        );
    }

    protected function internetQuotaTopupsExport($request)
    {
        $range = $request->get('range', 'bulanan');
        $query = InternetQuotaTopup::with('creator', 'wifiPayment');
        if ($range === 'harian') {
            $query->whereDate('payment_date', Carbon::today());
        } elseif ($range === 'mingguan') {
            $query->whereBetween('payment_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($range === 'tahunan') {
            $year = $request->get('quota_topup_year', now()->year);
            $query->whereYear('payment_date', $year);
        } else {
            $month = $request->get('quota_topup_month', now()->format('Y-m'));
            $start = Carbon::parse($month.'-01')->startOfMonth();
            $end = $start->copy()->endOfMonth();
            $query->whereBetween('payment_date', [$start, $end]);
        }
        $data = $query->orderBy('payment_date', 'desc')
            ->get()->map(fn ($t) => [
                'Tanggal Bayar' => $t->payment_date->format('d/m/Y'),
                'Internet' => $t->wifiPayment?->nama_internet ?? '-',
                'Periode' => $t->period,
                'Jumlah GB' => $t->amount_gb,
                'Nominal' => $t->nominal,
                'Oleh' => $t->creator?->name ?? '-',
                'Bukti' => $t->bukti_bayar ? route('files.show', $t->bukti_bayar) : '-',
                'Catatan' => $t->notes ?: 'Tidak ada catatan',
            ]);

        return Excel::download(
            new DataExport(collect($data), array_keys($data->first() ?? []), 'Riwayat Pembelian Kuota Internet', 'Kuota Internet'),
            'Riwayat_Pembelian_Kuota.xlsx'
        );
    }

    protected function internetQuotaReadingsExport($request)
    {
        $range = $request->get('range', 'bulanan');
        $query = InternetQuotaReading::with('checker', 'wifiPayment');
        if ($range === 'harian') {
            $query->whereDate('checked_date', Carbon::today());
        } elseif ($range === 'mingguan') {
            $query->whereBetween('checked_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($range === 'tahunan') {
            $year = $request->get('quota_reading_year', now()->year);
            $query->whereYear('checked_date', $year);
        } else {
            $month = $request->get('quota_reading_month', now()->format('Y-m'));
            $start = Carbon::parse($month.'-01')->startOfMonth();
            $end = $start->copy()->endOfMonth();
            $query->whereBetween('checked_date', [$start, $end]);
        }

        $latestTopup = InternetQuotaTopup::orderBy('payment_date', 'desc')->first();
        $capacityGb = $latestTopup ? (float) $latestTopup->amount_gb : 0;

        $data = $query->orderBy('checked_date', 'desc')
            ->get()->map(fn ($r) => [
                'Tanggal Check' => $r->checked_date->format('d/m/Y'),
                'Internet' => $r->wifiPayment?->nama_internet ?? '-',
                'Sisa GB' => $r->remaining_gb,
                'Terpakai' => max(0, $capacityGb - (float) $r->remaining_gb),
                'Status' => $r->status ?? '-',
                'Pengecek' => $r->checker?->name ?? '-',
                'Catatan' => $r->notes ?: 'Tidak ada catatan',
            ]);

        return Excel::download(
            new DataExport(collect($data), array_keys($data->first() ?? []), 'Data Pengecekan Kuota Internet', 'Kuota Internet'),
            'Data_Kuota_Internet.xlsx'
        );
    }
}
