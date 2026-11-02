<?php

use App\Http\Controllers\Admin\AdminAccountController;
use App\Http\Controllers\Admin\AsetMesController;
use App\Http\Controllers\Admin\AsetRukoController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\DigitalAssetController;
use App\Http\Controllers\Admin\EmailSettingsController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\ItTicketController;
use App\Http\Controllers\Admin\MeetingController as AdminMeetingController;
use App\Http\Controllers\Admin\MomController as AdminMomController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PeralatanKantorController;
use App\Http\Controllers\Admin\RoomController as AdminRoomController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SimCardController;
use App\Http\Controllers\Admin\SosialMediaController;
use App\Http\Controllers\Admin\TeamCompositionController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Admin\VehiclePajakRequestController;
use App\Http\Controllers\Admin\WeeklyMeetingController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\Leader\AsetMesController as KoordinatorAsetMesController;
use App\Http\Controllers\Leader\AsetTimController as KoordinatorAsetTimController;
use App\Http\Controllers\Leader\AssetSayaController;
use App\Http\Controllers\Leader\DashboardController as KoordinatorDashboard;
use App\Http\Controllers\Leader\MeetingController as KoordinatorMeetingController;
use App\Http\Controllers\Leader\MomController;
use App\Http\Controllers\Leader\RecordingController;
use App\Http\Controllers\MomExportController;
use App\Http\Controllers\OverrideRequestController;
use App\Http\Controllers\PaymentApprovalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicAssetController;
use App\Http\Controllers\PushController;
use App\Http\Controllers\RealtimeController;
use App\Http\Controllers\User\DashboardController as UserDashboard;
use App\Http\Controllers\WeeklySessionController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('files/{path}', [FileController::class, 'show'])->name('files.show')->where('path', '.*');
Route::get('files/check/{path}', [FileController::class, 'check'])->name('files.check')->where('path', '.*');

// Public Asset Detail — tanpa login
Route::get('/aset/{kode_aset}', [PublicAssetController::class, 'show'])->name('public.asset.show');
Route::get('/aset/{kode_aset}/qr', [PublicAssetController::class, 'qrCode'])->name('public.asset.qr');

Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;
        if (in_array($role, User::FULL_ACCESS_ROLES)) {
            return redirect()->route('admin.dashboard');
        }

        if ($role === 'admin_ga') {
            return redirect()->route('koordinator.dashboard');
        }

        return redirect()->route($role.'.dashboard');
    }

    return redirect()->route('login');
});

// Admin Routes — semua role full access
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::resource('teams', TeamController::class);
    Route::post('teams/{team}/members', [TeamController::class, 'addMember'])->name('teams.members.store');
    Route::delete('teams/{team}/members/{user}', [TeamController::class, 'removeMember'])->name('teams.members.destroy');
    Route::resource('assets', AssetController::class);
    Route::get('moms', [AdminMomController::class, 'index'])->name('moms.index');
    Route::resource('vehicles', VehicleController::class)->except(['create', 'show', 'edit']);
    Route::patch('vehicles/{vehicle}/status', [VehicleController::class, 'updateStatus'])->name('vehicles.status');
    Route::post('vehicles/{vehicle}/pajak-request', [VehiclePajakRequestController::class, 'store'])->name('vehicles.pajak-request.store');
    Route::get('vehicles/pajak-requests/pending', [VehiclePajakRequestController::class, 'pending'])->name('vehicles.pajak-requests.pending');
    Route::post('vehicles/pajak-requests/{id}/approve', [VehiclePajakRequestController::class, 'approve'])->name('vehicles.pajak-requests.approve');
    Route::post('vehicles/pajak-requests/{id}/reject', [VehiclePajakRequestController::class, 'reject'])->name('vehicles.pajak-requests.reject');
    Route::resource('digital-assets', DigitalAssetController::class)->except(['create', 'show', 'edit']);
    Route::resource('sim-cards', SimCardController::class)->except(['create', 'show', 'edit']);
    Route::resource('peralatan-kantor', PeralatanKantorController::class)->except(['create', 'show', 'edit']);
    Route::post('peralatan-kantor/{peralatan_kantor}/maintenance', [PeralatanKantorController::class, 'storeMaintenance'])->name('peralatan-kantor.maintenance.store');
    Route::post('peralatan-kantor/import', [PeralatanKantorController::class, 'import'])->name('peralatan-kantor.import');
    Route::get('peralatan-kantor/template', [PeralatanKantorController::class, 'downloadTemplate'])->name('peralatan-kantor.template');
    Route::post('peralatan-kantor/scan', [PeralatanKantorController::class, 'scan'])->name('peralatan-kantor.scan');
    Route::post('peralatan-kantor/reset', [PeralatanKantorController::class, 'resetData'])->name('peralatan-kantor.reset');
    Route::patch('peralatan-kantor/{peralatan_kantor}/barcode-ditempel', [PeralatanKantorController::class, 'toggleBarcodeDitempel'])->name('peralatan-kantor.barcode-ditempel');
    Route::get('peralatan-kantor/{peralatan_kantor}/json', [PeralatanKantorController::class, 'showJson'])->name('peralatan-kantor.show-json');
    Route::resource('sosial-media', SosialMediaController::class)->except(['create', 'show', 'edit']);
    Route::resource('ruko', AsetRukoController::class)->except(['create', 'show', 'edit']);
    Route::resource('aset-mes', AsetMesController::class)->except(['create', 'show', 'edit'])->parameters(['aset-mes' => 'asetMes']);
    Route::get('pembayaran', [PaymentController::class, 'index'])->name('pembayaran.index');
    Route::post('pembayaran', [PaymentController::class, 'store'])->name('pembayaran.store');
    Route::put('pembayaran/{id}', [PaymentController::class, 'update'])->name('pembayaran.update');
    Route::delete('pembayaran/{id}', [PaymentController::class, 'destroy'])->name('pembayaran.destroy');
    Route::post('pembayaran/ipl-bulk', [PaymentController::class, 'bulkIpl'])->name('pembayaran.ipl-bulk');
    Route::post('token-reading', [PaymentController::class, 'storeTokenReading'])->name('pembayaran.token-reading.store');
    Route::put('token-reading/{id}', [PaymentController::class, 'updateTokenReading'])->name('pembayaran.token-reading.update');
    Route::delete('token-reading/{id}', [PaymentController::class, 'destroyTokenReading'])->name('pembayaran.token-reading.destroy');
    Route::post('token-topup', [PaymentController::class, 'storeTokenPayment'])->name('pembayaran.token-topup.store');
    Route::put('token-topup/{id}', [PaymentController::class, 'updateTokenPayment'])->name('pembayaran.token-topup.update');
    Route::delete('token-topup/{id}', [PaymentController::class, 'destroyTokenPayment'])->name('pembayaran.token-topup.destroy');
    Route::post('internet-usage', [PaymentController::class, 'storeInternetUsage'])->name('pembayaran.internet-usage.store');
    Route::delete('internet-usage/{id}', [PaymentController::class, 'destroyInternetUsage'])->name('pembayaran.internet-usage.destroy');
    Route::post('internet-quota-topup', [PaymentController::class, 'storeInternetQuotaTopup'])->name('pembayaran.internet-quota-topup.store');
    Route::put('internet-quota-topup/{id}', [PaymentController::class, 'updateInternetQuotaTopup'])->name('pembayaran.internet-quota-topup.update');
    Route::delete('internet-quota-topup/{id}', [PaymentController::class, 'destroyInternetQuotaTopup'])->name('pembayaran.internet-quota-topup.destroy');
    Route::post('internet-quota-reading', [PaymentController::class, 'storeInternetQuotaReading'])->name('pembayaran.internet-quota-reading.store');
    Route::put('internet-quota-reading/{id}', [PaymentController::class, 'updateInternetQuotaReading'])->name('pembayaran.internet-quota-reading.update');
    Route::delete('internet-quota-reading/{id}', [PaymentController::class, 'destroyInternetQuotaReading'])->name('pembayaran.internet-quota-reading.destroy');
    Route::post('pembayaran/ipl-ruko/bulk', [PaymentController::class, 'storeBulkIplRuko'])->name('pembayaran.ipl-ruko.bulk');
    Route::get('payment-approvals', [PaymentApprovalController::class, 'index'])->name('payment-approvals.index');
    Route::post('payment-approvals/{id}/approve', [PaymentApprovalController::class, 'approve'])->name('payment-approvals.approve');
    Route::post('payment-approvals/approve-all', [PaymentApprovalController::class, 'approveAll'])->name('payment-approvals.approve-all');
    Route::post('payment-approvals/{id}/reject', [PaymentApprovalController::class, 'reject'])->name('payment-approvals.reject');
    Route::get('payment-approvals/export', [PaymentApprovalController::class, 'exportApprovals'])->name('payment-approvals.export');
    Route::post('payment-approvals/reset', [PaymentApprovalController::class, 'resetData'])->name('payment-approvals.reset');
    Route::get('export', [ExportController::class, 'export'])->name('export');

    // Rekap Rapat (Recording)
    Route::get('recordings', [RecordingController::class, 'index'])->name('recordings.index');
    Route::get('recordings/create', [RecordingController::class, 'create'])->name('recordings.create');
    Route::post('recordings', [RecordingController::class, 'store'])->name('recordings.store');
    Route::get('recordings/{recording}', [RecordingController::class, 'show'])->name('recordings.show');
    Route::get('recordings/{recording}/edit', [RecordingController::class, 'edit'])->name('recordings.edit');
    Route::put('recordings/{recording}', [RecordingController::class, 'update'])->name('recordings.update');
    Route::delete('recordings/{recording}', [RecordingController::class, 'destroy'])->name('recordings.destroy');

    // // Chat — hidden
    // Route::get('/chat/conversations', [ChatController::class, 'conversations'])->name('chat.conversations');
    // Route::get('/chat/messages', [ChatController::class, 'messages'])->name('chat.messages');
    // Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');
    // Route::get('/chat/unread', [ChatController::class, 'unreadCount'])->name('chat.unread');

    Route::get('settings/email', [EmailSettingsController::class, 'index'])->name('settings.email');
    Route::put('settings/email', [EmailSettingsController::class, 'update'])->name('settings.email.update');
});

// Hanya Admin & HR
Route::middleware(['auth', 'admin_hr'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('team-compositions', [TeamCompositionController::class, 'index'])->name('team-compositions.index');
    Route::put('team-compositions', [TeamCompositionController::class, 'update'])->name('team-compositions.update');
    Route::put('team-compositions/{teamComposition}', [TeamCompositionController::class, 'updateSingle'])->name('team-compositions.update-single');
    Route::delete('team-compositions/{teamComposition}', [TeamCompositionController::class, 'destroy'])->name('team-compositions.destroy');
    Route::resource('rooms', AdminRoomController::class);
    Route::get('meetings', [AdminMeetingController::class, 'index'])->name('meetings.index');
    Route::get('meetings/{meeting}', [AdminMeetingController::class, 'show'])->name('meetings.show');
    Route::patch('meetings/{meeting}/approve', [AdminMeetingController::class, 'approve'])->name('meetings.approve');
    Route::patch('meetings/{meeting}/reject', [AdminMeetingController::class, 'reject'])->name('meetings.reject');
    Route::patch('meetings/{meeting}', [AdminMeetingController::class, 'update'])->name('meetings.update');
    Route::delete('meetings/{meeting}', [AdminMeetingController::class, 'destroy'])->name('meetings.destroy');
    Route::resource('weekly-meetings', WeeklyMeetingController::class);
});

// Kelola Akun — hanya Admin Master dan HR
Route::middleware(['auth', 'manage_accounts'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('admins', AdminAccountController::class);
    Route::post('users/karyawan', [UserController::class, 'storeKaryawan'])->name('users.karyawan.store');
    Route::put('users/karyawan/{karyawan}', [UserController::class, 'updateKaryawan'])->name('users.karyawan.update');
    Route::delete('users/karyawan/{karyawan}', [UserController::class, 'destroyKaryawan'])->name('users.karyawan.destroy');
    Route::get('users/karyawan/template', [UserController::class, 'downloadKaryawanTemplate'])->name('users.karyawan.template');
    Route::post('users/karyawan/import', [UserController::class, 'importKaryawan'])->name('users.karyawan.import');
    Route::get('users/nik/template', [UserController::class, 'downloadNikTemplate'])->name('users.nik.template');
    Route::post('users/nik/import', [UserController::class, 'importNik'])->name('users.nik.import');
    Route::post('admins/karyawan', [AdminAccountController::class, 'storeKaryawan'])->name('admins.karyawan.store');
    Route::put('admins/karyawan/{karyawan}', [AdminAccountController::class, 'updateKaryawan'])->name('admins.karyawan.update');
    Route::delete('admins/karyawan/{karyawan}', [AdminAccountController::class, 'destroyKaryawan'])->name('admins.karyawan.destroy');
});

// Koordinator Routes
Route::middleware(['auth', 'leader'])->prefix('koordinator')->name('koordinator.')->group(function () {
    Route::get('/dashboard', [KoordinatorDashboard::class, 'index'])->name('dashboard');
    Route::resource('meetings', KoordinatorMeetingController::class);
    Route::patch('meetings/{meeting}/confirm', [KoordinatorMeetingController::class, 'confirm'])->name('meetings.confirm');
    Route::patch('meetings/{meeting}/cancel', [KoordinatorMeetingController::class, 'cancel'])->name('meetings.cancel');
    Route::patch('meetings/{meeting}/finish', [KoordinatorMeetingController::class, 'finish'])->name('meetings.finish');
    Route::get('moms', [MomController::class, 'index'])->name('mom.index');
    Route::resource('meetings.mom', MomController::class)->shallow();
    Route::patch('mom/{mom}/send', [MomController::class, 'send'])->name('mom.send');
    Route::post('mom/{mom}/upload-file', [MomController::class, 'uploadFile'])->name('mom.upload-file');
    Route::get('recordings', [RecordingController::class, 'index'])->name('recordings.index');
    Route::get('recordings/create', [RecordingController::class, 'create'])->name('recordings.create');
    Route::post('recordings', [RecordingController::class, 'store'])->name('recordings.store');
    Route::get('recordings/{recording}', [RecordingController::class, 'show'])->name('recordings.show');
    Route::get('recordings/{recording}/edit', [RecordingController::class, 'edit'])->name('recordings.edit');
    Route::put('recordings/{recording}', [RecordingController::class, 'update'])->name('recordings.update');
    Route::delete('recordings/{recording}', [RecordingController::class, 'destroy'])->name('recordings.destroy');
    Route::post('meetings/{meeting}/upload-file', [KoordinatorMeetingController::class, 'uploadFile'])->name('meetings.upload-file');
    Route::get('asset-saya', [AssetSayaController::class, 'index'])->name('asset-saya.index');
    Route::post('asset-saya', [AssetSayaController::class, 'store'])->name('asset-saya.store');
    Route::put('asset-saya/{kategori}/{id}', [AssetSayaController::class, 'update'])->name('asset-saya.update');
    Route::delete('asset-saya/{kategori}/{id}', [AssetSayaController::class, 'destroy'])->name('asset-saya.destroy');
    Route::get('aset-tim', [KoordinatorAsetTimController::class, 'index'])->name('aset-tim.index');
    Route::post('aset-tim', [KoordinatorAsetTimController::class, 'store'])->name('aset-tim.store');
    Route::put('aset-tim/{asetTim}', [KoordinatorAsetTimController::class, 'update'])->name('aset-tim.update');
    Route::delete('aset-tim/{asetTim}', [KoordinatorAsetTimController::class, 'destroy'])->name('aset-tim.destroy');
    Route::get('aset-mes', [KoordinatorAsetMesController::class, 'index'])->name('aset-mes.index');
    Route::post('aset-mes', [KoordinatorAsetMesController::class, 'store'])->name('aset-mes.store');
    Route::put('aset-mes/{asetMes}', [KoordinatorAsetMesController::class, 'update'])->name('aset-mes.update');
    Route::delete('aset-mes/{asetMes}', [KoordinatorAsetMesController::class, 'destroy'])->name('aset-mes.destroy');
});

// User Routes
Route::middleware(['auth', 'role:user,koordinator,admin,head_of_store,gm,hr,ceo'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserDashboard::class, 'index'])->name('dashboard');
});

// Payment Approval — Staff, Koordinator, HR, Admin submit
Route::middleware(['auth', 'role:user,koordinator,hr,admin,admin_ga,head_of_store,gm,ceo,assistant_manager'])->prefix('payment-approval')->name('payment-approval.')->group(function () {
    Route::get('tagihan', [PaymentApprovalController::class, 'tagihan'])->name('tagihan');
    Route::post('tagihan/{id}/bayar', [PaymentApprovalController::class, 'bayar'])->name('tagihan.bayar');
    Route::get('create', [PaymentApprovalController::class, 'create'])->name('create');
    Route::post('/', [PaymentApprovalController::class, 'store'])->name('store');
    Route::get('/', [PaymentApprovalController::class, 'myRequests'])->name('status');
    Route::get('export', [PaymentApprovalController::class, 'exportStatus'])->name('export');
    Route::get('export-tagihan', [PaymentApprovalController::class, 'exportTagihan'])->name('export-tagihan');
});

// Calendar & Invitation (semua role)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
    Route::get('/calendar/events', [CalendarController::class, 'events'])->name('calendar.events');
    Route::get('/push/vapid-key', [PushController::class, 'vapidPublicKey'])->name('push.vapid');
    Route::post('/push/subscribe', [PushController::class, 'subscribe'])->name('push.subscribe');
    Route::post('/push/unsubscribe', [PushController::class, 'unsubscribe'])->name('push.unsubscribe');
    Route::get('/realtime/meetings', [RealtimeController::class, 'meetings'])->name('realtime.meetings');
    Route::get('/realtime/weekly', [RealtimeController::class, 'weeklySessions'])->name('realtime.weekly');
    Route::get('/realtime/notif', [RealtimeController::class, 'notifCount'])->name('realtime.notif');
    Route::get('/realtime/dashboard', [RealtimeController::class, 'dashboardStats'])->name('realtime.dashboard');
    Route::get('/realtime/notifications', [RealtimeController::class, 'notifications'])->name('realtime.notifications');
    Route::post('/realtime/notifications/read', [RealtimeController::class, 'markRead'])->name('realtime.notifications.read');
    Route::post('/realtime/notifications/{id}/read', [RealtimeController::class, 'markReadSingle'])->name('realtime.notifications.read.single');
    Route::get('/undangan/{invitation}', [InvitationController::class, 'show'])->name('invitation.show');
    Route::get('/undangan', [InvitationController::class, 'index'])->name('invitation.index');

    // Weekly Meeting Sessions
    Route::get('/weekly-undangan', [WeeklySessionController::class, 'index'])->name('weekly.index');
    Route::get('/weekly-undangan/{invitation}', [WeeklySessionController::class, 'show'])->name('weekly.show');
    Route::post('/weekly-session/{session}/contribute', [WeeklySessionController::class, 'contribute'])->name('weekly.contribute');
    Route::post('/weekly-session/{session}/extend', [WeeklySessionController::class, 'extend'])->name('weekly.extend');
    Route::post('/weekly-session/{session}/complete', [WeeklySessionController::class, 'complete'])->name('weekly.complete');

    // Ticketing IT — semua role bisa akses, controller handle permission
    Route::get('it/tickets', [ItTicketController::class, 'index'])->name('it-tickets.index');
    Route::post('it/tickets', [ItTicketController::class, 'store'])->name('it-tickets.store');
    Route::patch('it/tickets/{ticket}', [ItTicketController::class, 'update'])->name('it-tickets.update');
    Route::delete('it/tickets/{ticket}', [ItTicketController::class, 'destroy'])->name('it-tickets.destroy');
    Route::post('it/tickets/{ticket}/feedback', [ItTicketController::class, 'feedback'])->name('it-tickets.feedback');

    // Override Routes
    Route::get('/override/create', [OverrideRequestController::class, 'create'])->name('override.create');
    Route::post('/override', [OverrideRequestController::class, 'store'])->name('override.store');
    Route::get('/override/{override}', [OverrideRequestController::class, 'show'])->name('override.show');
    Route::patch('/override/{override}/accept', [OverrideRequestController::class, 'accept'])->name('override.accept');
    Route::patch('/override/{override}/reject', [OverrideRequestController::class, 'reject'])->name('override.reject');

    // MOM Export
    Route::get('/mom/{mom}/export', [MomExportController::class, 'export'])->name('mom.export');
});
