@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
    <li class="breadcrumb-item active">Log Harian</li>
@endsection

@section('content')
{{-- ── Stats Row ── --}}
<div class="row mb-4">
    <div class="col-6 col-md-3 mb-3 mb-md-0">
        <div class="p-3 bg-white dark:bg-[#121212] border-3 border-black dark:border-white rounded-2xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_#FFE600] d-flex align-items-center gap-3">
            <div class="w-12 h-12 bg-[#0055FF] text-white border-2 border-black rounded-xl flex items-center justify-center font-fredoka font-black text-xl shrink-0" style="width: 48px; height: 48px;">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div>
                <div class="font-fredoka font-black text-2xl text-black dark:text-white mb-0 leading-none">{{ $todayCount }}</div>
                <div class="font-jakarta font-extrabold text-xs text-muted uppercase">Hari Ini</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3 mb-md-0">
        <div class="p-3 bg-white dark:bg-[#121212] border-3 border-black dark:border-white rounded-2xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_#FFE600] d-flex align-items-center gap-3">
            <div class="w-12 h-12 bg-[#00E676] text-black border-2 border-black rounded-xl flex items-center justify-center font-fredoka font-black text-xl shrink-0" style="width: 48px; height: 48px;">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div>
                <div class="font-fredoka font-black text-2xl text-black dark:text-white mb-0 leading-none">{{ $monthCount }}</div>
                <div class="font-jakarta font-extrabold text-xs text-muted uppercase">Bulan Ini</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3 mb-md-0">
        <div class="p-3 bg-white dark:bg-[#121212] border-3 border-black dark:border-white rounded-2xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_#FFE600] d-flex align-items-center gap-3">
            <div class="w-12 h-12 bg-[#FFE600] text-black border-2 border-black rounded-xl flex items-center justify-center font-fredoka font-black text-xl shrink-0" style="width: 48px; height: 48px;">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <div class="font-fredoka font-black text-2xl text-black dark:text-white mb-0 leading-none">{{ $pendingCount }}</div>
                <div class="font-jakarta font-extrabold text-xs text-muted uppercase">Pending</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3 mb-md-0">
        <div class="p-3 bg-white dark:bg-[#121212] border-3 border-black dark:border-white rounded-2xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_#FFE600] d-flex align-items-center gap-3">
            <div class="w-12 h-12 bg-[#9000FF] text-white border-2 border-black rounded-xl flex items-center justify-center font-fredoka font-black text-xl shrink-0" style="width: 48px; height: 48px;">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div>
                <div class="font-fredoka font-black text-2xl text-black dark:text-white mb-0 leading-none">
                    @if($totalDuration >= 60)
                        {{ floor($totalDuration / 60) }}j {{ $totalDuration % 60 }}m
                    @else
                        {{ $totalDuration }}m
                    @endif
                </div>
                <div class="font-jakarta font-extrabold text-xs text-muted uppercase">Durasi Bulan Ini</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212] mb-4">

            {{-- Card Header --}}
            <div class="d-flex justify-content-between align-items-center border-b-4 border-black dark:border-white pb-3 mb-3 flex-wrap gap-2 w-100">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div>
                        <h3 class="font-fredoka font-black text-xl text-black dark:text-white uppercase mb-0 d-flex align-items-center gap-2">
                            Log Harian – Komplain Ad-Hoc 📖
                        </h3>
                        <p class="font-jakarta font-extrabold text-xs text-muted mb-0">Penanganan komplain di luar pengajuan tiket resmi</p>
                    </div>
                    {{-- Filter Toggle Button --}}
                    <button class="btn bg-[#FFE600] text-black border-2 border-black font-fredoka font-black rounded-xl text-xs px-3 py-1 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-[#FF007A] hover:text-white transition-all ml-2" type="button" data-toggle="collapse" data-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                        <i class="fas fa-filter mr-1"></i> {{ request()->anyFilled(['date_from','date_to','status','source','month']) ? 'Filter Aktif ▾' : 'Filter Log ▾' }}
                    </button>
                </div>

                <div class="d-flex align-items-center gap-2 ml-auto ms-auto">
                    @if(request()->anyFilled(['date_from','date_to','status','source','month']))
                        <a href="{{ route('daily-logs.index') }}" class="btn bg-gray-200 text-black border-2 border-black font-fredoka font-black rounded-xl px-3 py-1.5 text-xs shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-[#FF007A] hover:text-white transition-all d-inline-flex align-items-center gap-1">
                            <i class="fas fa-undo"></i> Reset
                        </a>
                    @endif
                    <a href="{{ route('daily-logs.create') }}" class="btn !bg-[#0055FF] !text-white border-3 border-black font-fredoka font-black rounded-2xl px-4 py-2 text-sm shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:!bg-[#FF007A] hover:!text-white active:translate-x-1 active:translate-y-1 transition-all d-inline-flex items-center gap-2">
                        <i class="fas fa-plus"></i> <span>+ Tambah Log</span>
                    </a>
                </div>
            </div>

            {{-- Collapsible Filter Form (Only expands when clicked or when filters are active - ZERO GAP when collapsed) --}}
            <div class="collapse {{ request()->anyFilled(['date_from','date_to','status','source','month']) ? 'show' : '' }} mb-4" id="filterCollapse">
                <form action="{{ route('daily-logs.index') }}" method="GET" class="p-3 bg-[#FFFBEA] dark:bg-[#1a1a1a] border-3 border-black dark:border-white rounded-2xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] font-jakarta font-extrabold text-sm">
                    <div class="form-row align-items-end">
                        <div class="col-6 col-md-2 mb-2">
                            <label class="small font-weight-bold text-black dark:text-white mb-1 d-block">Dari</label>
                            <input type="date" name="date_from" class="form-control border-2 border-black rounded-xl" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-6 col-md-2 mb-2">
                            <label class="small font-weight-bold text-black dark:text-white mb-1 d-block">Sampai</label>
                            <input type="date" name="date_to" class="form-control border-2 border-black rounded-xl" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-6 col-md-2 mb-2">
                            <label class="small font-weight-bold text-black dark:text-white mb-1 d-block">Status</label>
                            <select name="status" class="form-control border-2 border-black rounded-xl">
                                <option value="">Semua Status</option>
                                @foreach(\App\Models\DailyLog::statuses() as $s)
                                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-2 mb-2">
                            <label class="small font-weight-bold text-black dark:text-white mb-1 d-block">Sumber</label>
                            <select name="source" class="form-control border-2 border-black rounded-xl">
                                <option value="">Semua Sumber</option>
                                @foreach(\App\Models\DailyLog::sources() as $src)
                                    <option value="{{ $src }}" {{ request('source') === $src ? 'selected' : '' }}>{{ $src }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-2 mb-2">
                            <label class="small font-weight-bold text-black dark:text-white mb-1 d-block">Bulan</label>
                            <input type="month" name="month" class="form-control border-2 border-black rounded-xl" value="{{ request('month') }}">
                        </div>
                        <div class="col-6 col-md-2 mb-2 d-flex gap-2">
                            <button type="submit" class="btn bg-[#0055FF] text-white border-2 border-black font-fredoka font-black rounded-xl px-3 py-1.5 flex-fill">
                                <i class="fas fa-filter"></i> Terapkan
                            </button>
                            <a href="{{ route('daily-logs.index') }}" class="btn bg-gray-200 text-black border-2 border-black font-fredoka font-black rounded-xl px-3 py-1.5 flex-fill text-center" title="Reset">
                                <i class="fas fa-undo"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- ════ TABLE CONTAINER ════ --}}
            <div class="table-responsive">
                <table class="table border-3 border-black dark:border-white rounded-2xl w-100 text-sm font-jakarta font-extrabold mb-0">
                    <thead>
                        <tr class="bg-[#FFE600] text-black font-fredoka font-black uppercase border-b-3 border-black">
                            <th class="p-3" style="width:110px;">TANGGAL</th>
                            <th class="p-3">PELAPOR</th>
                            <th class="p-3 d-none d-lg-table-cell">DEPT.</th>
                            <th class="p-3">SUMBER</th>
                            <th class="p-3">MASALAH</th>
                            <th class="p-3 d-none d-lg-table-cell">DURASI</th>
                            <th class="p-3">STATUS</th>
                            <th class="p-3 text-right" style="width:120px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="p-3">
                                <span class="font-fredoka font-black text-black dark:text-white text-xs d-block">{{ $log->log_date->format('d M Y') }}</span>
                                <small class="text-muted font-mono text-[10px]">{{ $log->log_date->diffForHumans() }}</small>
                            </td>
                            <td class="p-3">
                                <span class="font-fredoka font-black text-black dark:text-white d-block">{{ $log->reporter_name }}</span>
                                @if($log->contact_info)
                                    <small class="text-muted font-mono text-xs d-block">{{ $log->contact_info }}</small>
                                @endif
                            </td>
                            <td class="p-3 d-none d-lg-table-cell text-muted text-xs">{{ $log->department ?? '-' }}</td>
                            <td class="p-3">
                                <span class="badge bg-white text-black border border-black font-fredoka font-black px-2 py-1 text-xs">
                                    <i class="{{ $log->source_icon }} mr-1"></i>{{ $log->source }}
                                </span>
                            </td>
                            <td class="p-3" style="max-width:240px;">
                                <span style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;" class="text-xs">
                                    {{ $log->issue_description }}
                                </span>
                            </td>
                            <td class="p-3 d-none d-lg-table-cell text-xs" style="white-space:nowrap;">
                                @if($log->duration_minutes)
                                    <i class="fas fa-stopwatch text-muted mr-1"></i>
                                    @php $d = $log->duration_minutes; @endphp
                                    {{ $d >= 60 ? floor($d/60).'j '.($d%60).'m' : $d.'m' }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="p-3">
                                @if($log->status === 'Selesai')
                                    <span class="badge bg-[#00E676] text-black border-2 border-black font-fredoka font-black px-2 py-1">Selesai</span>
                                @elseif($log->status === 'Proses')
                                    <span class="badge bg-[#0055FF] text-white border-2 border-black font-fredoka font-black px-2 py-1">Proses</span>
                                @elseif($log->status === 'Pending')
                                    <span class="badge bg-[#FFE600] text-black border-2 border-black font-fredoka font-black px-2 py-1">Pending</span>
                                @else
                                    <span class="badge bg-gray-400 text-black border-2 border-black font-fredoka font-black px-2 py-1">{{ $log->status }}</span>
                                @endif
                            </td>
                            <td class="p-3 text-right">
                                <div class="btn-group">
                                    <a href="{{ route('daily-logs.show', $log) }}" class="btn btn-sm bg-[#FFE600] text-black border-2 border-black font-fredoka font-black rounded-xl px-2 py-1 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] mr-1" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('daily-logs.edit', $log) }}" class="btn btn-sm bg-[#00E676] text-black border-2 border-black font-fredoka font-black rounded-xl px-2 py-1 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] mr-1" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm bg-[#FF007A] text-white border-2 border-black font-fredoka font-black rounded-xl px-2 py-1 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] btn-delete"
                                        data-id="{{ $log->id }}" data-name="{{ $log->reporter_name }}" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <form id="delete-form-{{ $log->id }}" action="{{ route('daily-logs.destroy', $log) }}" method="POST" class="d-none">
                                        @csrf @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center p-4 font-jakarta font-extrabold text-muted">
                                <i class="fas fa-book-open fa-2x text-muted mb-2 d-block"></i>
                                Belum ada log harian. <a href="{{ route('daily-logs.create') }}" class="text-[#0055FF] font-bold">Tambah sekarang</a>.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
            <div class="mt-3">
                {{ $logs->withQueryString()->links() }}
            </div>
            @endif

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.btn-delete').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const id   = this.dataset.id;
        const name = this.dataset.name;
        Swal.fire({
            title: 'Hapus Log?',
            html: `Log komplain dari <b>${name}</b> akan dihapus permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('delete-form-' + id);
                if (form) form.submit();
            }
        });
    });
});
</script>
@endpush
