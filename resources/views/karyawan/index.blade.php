@extends('layouts.app')

@section('title', 'Kelola Karyawan & Absensi')
@section('page-title', 'Kelola Karyawan & Absensi')

@section('content')
<div class="space-y-6">

    <!-- Section Header Row -->
    <div>
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Kelola Karyawan & Absensi</h1>
        <p class="text-sm text-slate-500 mt-1">
            Pantau kehadiran, serta tambahkan atau hapus akun akses karyawan (Kasir).
        </p>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg font-medium text-sm flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg font-medium text-sm flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm space-y-1">
            @foreach($errors->all() as $err)
                <p class="font-medium">• {{ $err }}</p>
            @endforeach
        </div>
    @endif

    <!-- Two-Column Grid: Add form & Employee Table -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <!-- Left: Add Employee Form -->
        <div class="card bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <h2 class="text-xl font-bold text-slate-900 mb-4">Tambah Karyawan</h2>
            
            <form action="{{ route('karyawan.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="username" class="form-label text-slate-700 font-medium">Username</label>
                    <input type="text" id="username" name="username" required placeholder="Masukkan username..." class="input-field mt-1 w-full" value="{{ old('username') }}">
                </div>

                <div>
                    <label for="password" class="form-label text-slate-700 font-medium">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Masukkan password..." class="input-field mt-1 w-full">
                </div>

                <div>
                    <label class="form-label text-slate-700 font-medium">Peran / Role</label>
                    <input type="text" value="Kasir" class="input-field mt-1 w-full bg-slate-100 text-slate-500 cursor-not-allowed select-none" readonly>
                    <input type="hidden" name="role" value="kasir">
                </div>

                <button type="submit" class="btn-primary w-full justify-center min-h-[44px] cursor-pointer mt-4">
                    Simpan Karyawan
                </button>
            </form>
        </div>

        <!-- Right: List of Employees Table -->
        <div class="lg:col-span-2 card p-0 overflow-hidden bg-white border border-slate-200 shadow-sm rounded-xl">
            <div class="p-6 border-b border-slate-100">
                <h2 class="text-xl font-bold text-slate-900">Daftar Akun Karyawan</h2>
            </div>
            
            @if($employees->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table-base">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Peran</th>
                                <th>Terdaftar Pada</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $emp)
                                <tr>
                                    <td class="font-bold text-slate-900 text-base">
                                        {{ $emp->username }}
                                    </td>
                                    <td>
                                        @if($emp->role === 'kasir')
                                            <span class="badge-yellow">Kasir</span>
                                        @elseif($emp->role === 'gudang')
                                            <span class="badge-gray">Gudang</span>
                                        @else
                                            <span class="badge-gray">{{ ucfirst($emp->role) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-slate-500 font-mono text-sm">
                                        {{ $emp->created_at ? $emp->created_at->format('d M Y, H:i') : '-' }}
                                    </td>
                                    <td class="text-right">
                                        <form action="{{ route('karyawan.destroy', $emp->user_id) }}" method="POST" class="inline" x-data>
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" x-on:click.prevent="if(confirm('Apakah Anda yakin ingin menghapus akun karyawan {{ $emp->username }}?')) $el.closest('form').submit()" class="btn-danger px-3 py-1.5 text-sm min-h-[36px] cursor-pointer items-center inline-flex gap-1.5">
                                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"></path>
                                                </svg>
                                                <span>Hapus</span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-12 text-center">
                    <svg class="w-16 h-16 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"></path>
                    </svg>
                    <span class="text-slate-400 font-medium text-sm">Belum ada karyawan terdaftar</span>
                </div>
            @endif
        </div>

    </div>

    <!-- Attendance Section -->
    <div class="card p-0 overflow-hidden bg-white border border-slate-200 shadow-sm rounded-xl">
        <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Log Absensi Karyawan</h2>
                <p class="text-xs text-slate-400 mt-0.5">Mencatat waktu kehadiran otomatis setiap kali karyawan melakukan login</p>
            </div>
            <span class="text-xs font-bold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-lg">Absen Masuk</span>
        </div>

        @if($attendances->count() > 0)
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Karyawan</th>
                            <th>Peran</th>
                            <th>Jam & Tanggal Login</th>
                            <th>Rentang Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendances as $att)
                            <tr>
                                <td class="font-bold text-slate-900">
                                    {{ $att->user?->username ?? 'Dihapus' }}
                                </td>
                                <td>
                                    @if($att->user)
                                        @if($att->user->role === 'kasir')
                                            <span class="badge-yellow">Kasir</span>
                                        @elseif($att->user->role === 'gudang')
                                            <span class="badge-gray">Gudang</span>
                                        @else
                                            <span class="badge-gray">{{ ucfirst($att->user->role) }}</span>
                                        @endif
                                    @else
                                        <span class="badge-gray">-</span>
                                    @endif
                                </td>
                                <td class="font-semibold text-slate-800 font-mono text-base">
                                    {{ $att->login_at ? $att->login_at->translatedFormat('l, d F Y — H:i:s') : '-' }} WIB
                                </td>
                                <td class="text-slate-500 font-medium text-sm">
                                    {{ $att->login_at ? $att->login_at->diffForHumans() : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Attendance Pagination -->
            <div class="px-6 py-4 flex flex-col md:flex-row items-center justify-between border-t border-slate-100 bg-slate-50/50 gap-4">
                <div class="text-sm text-slate-500 font-medium">
                    Menampilkan {{ $attendances->firstItem() ?? 0 }}–{{ $attendances->lastItem() ?? 0 }} dari {{ $attendances->total() }} riwayat absen
                </div>
                <div>
                    {{ $attendances->links() }}
                </div>
            </div>
        @else
            <div class="py-16 text-center">
                <svg class="w-20 h-20 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path>
                </svg>
                <h3 class="text-lg font-bold text-slate-800">Tidak ada riwayat absen</h3>
                <p class="text-slate-500 mt-1 text-sm">Absensi akan otomatis tercatat saat karyawan masuk/login ke dalam sistem</p>
            </div>
        @endif
    </div>

</div>
@endsection
