<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Browser Sessions</h6>
    </div>
    <div class="card-body">
        @if (count($sessions) > 0)
        <ul class="list-group mt-4">
            @foreach ($sessions as $session)
            <li class="list-group-item d-flex align-items-center">
                <div class="me-3">
                    @if ($session->agent->isDesktop())
                    <i class="fas fa-desktop text-secondary fa-lg"></i>
                    @else
                    <i class="fas fa-mobile-alt text-secondary fa-lg"></i>
                    @endif
                </div>
                <div>
                    <p class="mb-0 text-secondary">
                        {{ $session->agent->platform() ?? 'Unknown' }} - {{ $session->agent->browser() ?? 'Unknown' }}
                    </p>
                    <small class="text-muted">
                        {{ $session->ip_address }},
                        @if ($session->is_current_device)
                        <span class="text-success font-weight-bold">This device</span>
                        @else
                        Last active {{ $session->last_active }}
                        @endif
                    </small>
                </div>
            </li>
            @endforeach
        </ul>
        @else
        <p class="text-muted">No active sessions found.</p>
        @endif
    </div>
</div>