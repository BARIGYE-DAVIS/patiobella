@extends('layouts.store')

@section('title', 'Store Dashboard')

@section('page-title', 'Store Dashboard')

@section('content')
<style>
    .welcome-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 3rem 2rem;
        color: white;
        text-align: center;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        margin-top: 2rem;
    }
    .welcome-card h1 {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        font-weight: 600;
    }
    .welcome-card p {
        font-size: 1.1rem;
        opacity: 0.95;
        margin-bottom: 0.5rem;
    }
    .date-time {
        margin-top: 1.5rem;
        font-size: 1rem;
        opacity: 0.85;
        padding-top: 1rem;
        border-top: 1px solid rgba(255,255,255,0.2);
        display: inline-block;
    }
    .emoji {
        font-size: 3rem;
        margin-bottom: 1rem;
    }
</style>

<div class="welcome-card">
    <div class="emoji">
        🏪
    </div>
    <h1>
        Welcome, {{ Auth::user()->first_name ?? 'Store Manager' }}!
    </h1>
    <p>Welcome to the Store Management Dashboard</p>
    <p>Your central hub for managing inventory, requisitions, and store operations.</p>
    <div class="date-time">
        <i class="fas fa-calendar-alt me-2"></i>
        {{ now()->format('l, F d, Y') }} &nbsp;&nbsp;|&nbsp;&nbsp;
        <i class="fas fa-clock me-1"></i>
        {{ now()->format('h:i A') }}
    </div>
</div>

<div class="text-center mt-5">
    <div class="row g-3 justify-content-center">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-boxes fa-2x text-primary mb-2"></i>
                    <h6 class="text-muted">Inventory</h6>
                    <small class="text-muted">Manage stock items</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-clipboard-list fa-2x text-success mb-2"></i>
                    <h6 class="text-muted">Requisitions</h6>
                    <small class="text-muted">Process requests</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-2x text-info mb-2"></i>
                    <h6 class="text-muted">Reports</h6>
                    <small class="text-muted">View analytics</small>
                </div>
            </div>
        </div>
    </div>

    <p class="text-muted mt-4">
        <i class="fas fa-cog fa-spin"></i> Dashboard features are being loaded...
    </p>
</div>
@endsection
