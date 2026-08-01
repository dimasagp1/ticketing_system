@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
    <li class="breadcrumb-item active">Permintaan Proyek</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-4 border-black dark:border-white rounded-3xl p-4 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_#FFE600] bg-white dark:bg-[#121212] mb-4">
            
            <!-- Card Header -->
            <div class="d-flex justify-content-between align-items-center border-b-4 border-black dark:border-white pb-3 mb-3 flex-wrap gap-2">
                <div class="d-flex items-center gap-3">
                    <h3 class="font-fredoka font-black text-xl text-black dark:text-white uppercase mb-0">
                        Semua Permintaan Proyek 🎫
                    </h3>
                    <!-- Filter Toggle Button -->
                    <button class="btn bg-[#FFE600] text-black border-2 border-black font-fredoka font-black rounded-xl text-xs px-3 py-1 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-[#FF007A] hover:text-white transition-all" type="button" data-toggle="collapse" data-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                        <i class="fas fa-filter mr-1"></i> Filter Tiket
                    </button>
                </div>

                @if(auth()->user()->isClient())
                    <a href="{{ route('project-requests.create') }}" class="btn !bg-[#0055FF] !text-white border-3 border-black font-fredoka font-black rounded-2xl px-4 py-2 text-sm shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:!bg-[#FF007A] hover:!text-white active:translate-x-1 active:translate-y-1 transition-all d-inline-flex items-center gap-2">
                        <i class="fas fa-plus"></i> <span>+ Permintaan Baru</span>
                    </a>
                @endif
            </div>

            <!-- Collapsible Filter Form (Only expands when clicked or when filters are active) -->
            <div class="collapse {{ request()->anyFilled(['search', 'status', 'ticket_status', 'ticket_category', 'impact', 'urgency', 'sla_filter']) ? 'show' : '' }} mb-4" id="filterCollapse">
                <form method="GET" action="{{ route('project-requests.index') }}" class="p-3 bg-[#FFFBEA] dark:bg-[#1a1a1a] border-3 border-black dark:border-white rounded-2xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] font-jakarta font-extrabold text-sm">
                    <div class="form-row">
                        <div class="col-md-3 mb-2">
                            <input type="text" name="search" class="form-control border-2 border-black rounded-xl" placeholder="Cari tiket/proyek/klien..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2 mb-2">
                            @php($requestStatusOptions = \App\Models\ProjectRequest::requestStatusLabels())
                            <select name="status" class="form-control border-2 border-black rounded-xl">
                                <option value="">Status Permintaan</option>
                                @foreach($requestStatusOptions as $value => $label)
                                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <select name="ticket_status" class="form-control border-2 border-black rounded-xl">
                                <option value="">Status Tiket</option>
                                @foreach(\App\Models\ProjectRequest::ticketStatusLabels() as $value => $label)
                                    <option value="{{ $value }}" {{ request('ticket_status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <select name="ticket_category" class="form-control border-2 border-black rounded-xl">
                                <option value="">Kategori</option>
                                @foreach(
                                    [
                                        'incident' => 'Insiden',
                                        'service_request' => 'Permintaan Layanan',
                                        'access' => 'Akses',
                                        'bug' => 'Bug',
                                        'technical_support' => 'Dukungan Teknis',
                                        'other' => 'Lainnya',
                                    ] as $value => $label
                                )
                                    <option value="{{ $value }}" {{ request('ticket_category') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1 mb-2">
                            <select name="impact" class="form-control border-2 border-black rounded-xl">
                                <option value="">Dampak</option>
                                @foreach(['low' => 'Rendah', 'medium' => 'Sedang', 'high' => 'Tinggi', 'critical' => 'Kritis'] as $value => $label)
                                    <option value="{{ $value }}" {{ request('impact') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1 mb-2">
                            <select name="urgency" class="form-control border-2 border-black rounded-xl">
                                <option value="">Urgensi</option>
                                @foreach(['low' => 'Rendah', 'medium' => 'Sedang', 'high' => 'Tinggi', 'critical' => 'Kritis'] as $value => $label)
                                    <option value="{{ $value }}" {{ request('urgency') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1 mb-2">
                            <select name="sla_filter" class="form-control border-2 border-black rounded-xl">
                                <option value="">SLA</option>
                                <option value="overdue" {{ request('sla_filter') === 'overdue' ? 'selected' : '' }}>Terlambat</option>
                                <option value="today" {{ request('sla_filter') === 'today' ? 'selected' : '' }}>Jatuh Tempo Hari Ini</option>
                                <option value="at_risk_24h" {{ request('sla_filter') === 'at_risk_24h' ? 'selected' : '' }}>Risiko 24 Jam</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn bg-[#0055FF] text-white border-2 border-black font-fredoka font-black rounded-xl px-3 py-1.5">
                                    <i class="fas fa-filter"></i> Terapkan
                                </button>
                                <a href="{{ route('project-requests.index') }}" class="btn bg-gray-200 text-black border-2 border-black font-fredoka font-black rounded-xl px-3 py-1.5">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Table Container (Flush right below header) -->
            <div class="table-responsive">
                <table class="table table-hover align-middle data-table mb-0 border-3 border-black dark:border-white rounded-2xl w-100 font-jakarta font-extrabold text-sm">
                    <thead>
                        <tr class="bg-[#FFE600] text-black font-fredoka font-black uppercase border-b-3 border-black">
                            <th>ID</th>
                            <th>Ticket</th>
                            <th>Nama Proyek</th>
                            @if(!auth()->user()->isClient())
                                <th class="d-none d-lg-table-cell">Client</th>
                            @endif
                            <th class="d-none d-lg-table-cell">Kategori</th>
                            <th class="d-none d-xl-table-cell">Dampak/Urgensi</th>
                            <th>Status</th>
                            <th>Status Tiket</th>
                            <th>SLA Due</th>
                            <th class="d-none d-lg-table-cell">Diajukan</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $request)
                            <tr>
                                <td><span class="badge bg-black text-white border-1 border-black font-fredoka font-black">#{{ $request->id }}</span></td>
                                <td>
                                    <span class="font-fredoka font-black text-sm text-[#0055FF] dark:text-[#FFE600]">{{ $request->ticket_number ?? '-' }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('project-requests.show', $request) }}" class="font-fredoka font-black text-black dark:text-white hover:text-[#FF007A]">
                                        {{ $request->title }}
                                    </a>
                                </td>
                                @if(!auth()->user()->isClient())
                                    <td class="d-none d-lg-table-cell">
                                        <span class="font-jakarta font-bold text-xs">{{ $request->client->name ?? '-' }}</span>
                                    </td>
                                @endif
                                <td class="d-none d-lg-table-cell">
                                    <span class="badge bg-[#FFFBEA] text-black border-2 border-black font-fredoka font-black text-xs px-2 py-1">
                                        {{ $request->ticket_category_label }}
                                    </span>
                                </td>
                                <td class="d-none d-xl-table-cell">
                                    <span class="badge bg-[#FF007A] text-white border-2 border-black font-fredoka font-black text-xs px-2 py-1">
                                        {{ $request->impact_label }}/{{ $request->urgency_label }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-[#FFE600] text-black border-2 border-black font-fredoka font-black text-xs px-2 py-1">
                                        {{ $request->status_label }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-[#0055FF] text-white border-2 border-black font-fredoka font-black text-xs px-2 py-1">
                                        {{ $request->ticket_status_label }}
                                    </span>
                                </td>
                                <td>
                                    <span class="font-mono text-xs font-bold">{{ $request->sla_resolution_due_at ? $request->sla_resolution_due_at->format('d/m/Y H:i') : '-' }}</span>
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    <span class="font-mono text-xs text-muted">{{ $request->created_at->format('d/m/Y') }}</span>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('project-requests.show', $request) }}" class="btn btn-sm bg-[#00E676] text-black border-2 border-black font-fredoka font-black rounded-xl px-3 py-1 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-[#FFE600]">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->isClient() ? 10 : 11 }}" class="text-center p-4 font-jakarta font-extrabold text-muted">
                                    Tidak ada data yang tersedia
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Row -->
            <div class="mt-3">
                {{ $requests->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
