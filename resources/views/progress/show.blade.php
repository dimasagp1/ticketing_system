@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
    @if(auth()->user()->isClient())
        <li class="breadcrumb-item"><a href="{{ route('project-requests.index') }}">My Projects</a></li>
    @elseif(auth()->user()->isDeveloper())
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Antrian Saya</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('queues.index') }}">Queues</a></li>
    @endif
    <li class="breadcrumb-item active">{{ $queue->project_name }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-3">
        <!-- Project Info -->
        <div class="card support-shell-card mb-4">
            <div class="card-body box-profile px-4 pb-4 pt-4">
                <h3 class="profile-username font-weight-bold text-center">{{ $queue->project_name }}</h3>
                <p class="text-muted text-center">{{ $queue->assignedTo->name ?? 'Unassigned' }}</p>
                <div class="text-center mb-3">
                    @if($queue->status == 'Completed')
                        <span class="badge badge-success">Completed</span>
                    @elseif($queue->status == 'In Progress')
                        <span class="badge badge-primary">In Progress</span>
                    @else
                        <span class="badge badge-secondary">{{ $queue->status }}</span>
                    @endif
                </div>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Progress</b> <a class="float-right">{{ $queue->progress }}%</a>
                    </li>
                    <li class="list-group-item">
                        <b>Deadline</b> <a class="float-right">{{ $queue->deadline ? $queue->deadline->format('d M Y') : '-' }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Remaining</b> <a class="float-right">{{ $queue->getDaysRemaining() ?? '-' }} days</a>
                    </li>
                </ul>

                @if($queue->projectRequest)
                    <a href="{{ route('project-requests.show', $queue->projectRequest) }}" class="btn btn-primary btn-block font-weight-500 shadow-sm mt-4" style="border-radius: 0.5rem;"><b>View Project Details</b></a>
                @endif
            </div>
        </div>

        <!-- Description -->
        <div class="card support-shell-card mb-4">
            <div class="card-header border-0 bg-white pt-4 px-4 pb-2">
                <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">Description</h3>
            </div>
            <div class="card-body px-4 pb-4 pt-2">
                <p class="text-muted mb-0">{{ $queue->description }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <!-- Progress Steps -->
        <div class="card support-shell-card mb-4">
            <div class="card-header border-0 bg-white pt-4 px-4 pb-2">
                <ul class="nav nav-pills mt-2" style="gap: 0.5rem;">
                    <li class="nav-item"><a class="nav-link active" href="#timeline" data-toggle="tab">Timeline & Activity</a></li>
                    @if(auth()->user()->hasRole(['developer', 'admin', 'super_admin']))
                        <li class="nav-item"><a class="nav-link" href="#update" data-toggle="tab">Update Progress</a></li>
                    @endif
                </ul>
            </div>
            <div class="card-body px-4 pb-4 pt-2">
                <div class="tab-content mt-3">
                    <div class="active tab-pane" id="timeline">
                        <!-- The timeline -->
                        <div class="timeline timeline-inverse">
                            @forelse($queue->progressLogs()->orderBy('created_at', 'desc')->get() as $log)
                                <div class="time-label">
                                    <span class="bg-{{ $log->completed_at ? 'success' : 'primary' }}">
                                        {{ $log->created_at->format('d M Y') }}
                                    </span>
                                </div>
                                
                                <div>
                                    <i class="fas fa-{{ $log->projectStage->icon ?? 'tasks' }} bg-primary"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="far fa-clock"></i> {{ $log->created_at->format('H:i') }}</span>
                                        <h3 class="timeline-header"><a href="#">{{ $log->updatedBy->name }}</a> updated: {{ $log->projectStage->name }}</h3>
                                        <div class="timeline-body">
                                            <p>{{ $log->activity_description }}</p>
                                            <div class="progress progress-xs">
                                                <div class="progress-bar bg-success" style="width: {{ $log->progress_percentage }}%"></div>
                                            </div>
                                            <small class="badge badge-light">{{ $log->progress_percentage }}% Complete</small>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div>
                                    <i class="fas fa-info bg-gray"></i>
                                    <div class="timeline-item">
                                        <h3 class="timeline-header">Belum ada aktivitas tercatat</h3>
                                        <div class="timeline-body text-muted">Belum ada update dari tim. Aktivitas akan muncul di sini.</div>
                                    </div>
                                </div>
                            @endforelse
                
                            @if($queue->created_at)
                                <div class="time-label">
                                    <span class="bg-secondary">{{ $queue->created_at->format('d M Y') }}</span>
                                </div>
                                <div>
                                    <i class="fas fa-plus bg-secondary"></i>
                                    <div class="timeline-item">
                                        <h3 class="timeline-header">Project Created/Queued</h3>
                                    </div>
                                </div>
                            @endif
                            
                            <div>
                                <i class="far fa-clock bg-gray"></i>
                            </div>
                        </div>
                    </div>
                   
                    @if(auth()->user()->hasRole(['developer', 'admin', 'super_admin']))
                        <div class="tab-pane" id="update">
                            <form action="{{ route('progress.update-stage', $queue) }}" method="POST" class="form-horizontal">
                                @csrf
                                <div class="form-group row">
                                    <label for="stage_id" class="col-sm-2 col-form-label">Current Stage</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" name="stage_id" id="stage_id">
                                            @foreach($stages as $stage)
                                                <option value="{{ $stage->id }}" {{ ($currentStage->project_stage_id ?? '') == $stage->id ? 'selected' : '' }}>
                                                    {{ $stage->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="progress_percentage" class="col-sm-2 col-form-label">Progress %</label>
                                    <div class="col-sm-10">
                                        <input type="number" class="form-control" id="progress_percentage" name="progress_percentage" 
                                               min="{{ $queue->progress }}" max="100" value="{{ $queue->progress }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="activity_description" class="col-sm-2 col-form-label">Activity Description</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" id="activity_description" name="activity_description" rows="3" placeholder="Describe the work done..."></textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="offset-sm-2 col-sm-10">
                                        <button type="submit" class="btn btn-danger">Update Progress</button>
                                    </div>
                                </div>
                            </form>
                            
                            <hr>
                            
                            <h4>Quick Log</h4>
                            <form action="{{ route('progress.log-activity', $queue) }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label>Add Activity Log (without changing stage)</label>
                                    <textarea class="form-control" name="activity_description" rows="2" placeholder="Quick update..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-secondary btn-sm">Log Activity</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
