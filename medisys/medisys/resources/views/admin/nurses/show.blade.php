@extends('layouts.app')
@section('page-title', 'Nurse Details - ' . $nurse->name)

@section('content')
<div class="page-header">
  <h1><i class="fas fa-user-nurse"></i> Nurse Details</h1>
  <div class="header-info">
    <p>Nurse: <strong>{{ $nurse->name }}</strong></p>
    <p>ID: {{ $nurse->code }}</p>
  </div>
  <div class="header-actions">
    <a href="{{ route('admin.nurses.edit', $nurse) }}" class="btn btn-warning">
      <i class="fas fa-edit"></i> Edit Nurse
    </a>
    <a href="{{ route('admin.nurses.index') }}" class="btn btn-secondary">
      <i class="fas fa-arrow-left"></i> Back to Nurses
    </a>
  </div>
</div>

<div class="nurse-profile-container">
  <div class="profile-card">
    <div class="profile-header">
      <div class="profile-avatar">
        <i class="fas fa-user-nurse"></i>
      </div>
      <div class="profile-info">
        <h2>{{ $nurse->name }}</h2>
        <p class="profile-code">{{ $nurse->code }}</p>
        <div class="profile-status">
          <span class="badge {{ $nurse->is_active ? 'badge-success' : 'badge-warning' }}">
            {{ $nurse->is_active ? 'Active' : 'Inactive' }}
          </span>
        </div>
      </div>
      <div class="profile-actions">
        <form action="{{ route('admin.nurses.toggle-status', $nurse) }}" method="POST">
          @csrf
          @method('PATCH')
          <button type="submit" class="btn {{ $nurse->is_active ? 'btn-secondary' : 'btn-success' }}">
            <i class="fas {{ $nurse->is_active ? 'fa-pause' : 'fa-play' }}"></i>
            {{ $nurse->is_active ? 'Deactivate' : 'Activate' }}
          </button>
        </form>
      </div>
    </div>

    <div class="profile-content">
      <div class="info-sections">
        <!-- Basic Information -->
        <div class="info-section">
          <h3><i class="fas fa-user"></i> Basic Information</h3>
          <div class="info-grid">
            <div class="info-item">
              <label>Full Name</label>
              <span>{{ $nurse->name }}</span>
            </div>
            <div class="info-item">
              <label>Email Address</label>
              <span>{{ $nurse->email }}</span>
            </div>
            <div class="info-item">
              <label>Username</label>
              <span>{{ $nurse->username }}</span>
            </div>
            <div class="info-item">
              <label>Phone Number</label>
              <span>{{ $nurse->phone ?? 'Not provided' }}</span>
            </div>
          </div>
        </div>

        <!-- Professional Information -->
        <div class="info-section">
          <h3><i class="fas fa-hospital"></i> Professional Information</h3>
          <div class="info-grid">
            <div class="info-item">
              <label>Department</label>
              <span>{{ $nurse->department ?? 'Not assigned' }}</span>
            </div>
            <div class="info-item">
              <label>License Number</label>
              <span>{{ $nurse->license_number ?? 'Not provided' }}</span>
            </div>
            <div class="info-item">
              <label>Role</label>
              <span class="badge badge-info">Nurse</span>
            </div>
            <div class="info-item">
              <label>Account Status</label>
              <span class="badge {{ $nurse->is_active ? 'badge-success' : 'badge-warning' }}">
                {{ $nurse->is_active ? 'Active' : 'Inactive' }}
              </span>
            </div>
          </div>
        </div>

        <!-- Contact Information -->
        <div class="info-section">
          <h3><i class="fas fa-map-marker-alt"></i> Contact Information</h3>
          <div class="info-item full-width">
            <label>Address</label>
            <span>{{ $nurse->address ?? 'Not provided' }}</span>
          </div>
        </div>

        <!-- Account Information -->
        <div class="info-section">
          <h3><i class="fas fa-info-circle"></i> Account Information</h3>
          <div class="info-grid">
            <div class="info-item">
              <label>Nurse ID</label>
              <span>{{ $nurse->code }}</span>
            </div>
            <div class="info-item">
              <label>Account Created</label>
              <span>{{ $nurse->created_at->format('M d, Y - h:i A') }}</span>
            </div>
            <div class="info-item">
              <label>Last Updated</label>
              <span>{{ $nurse->updated_at->format('M d, Y - h:i A') }}</span>
            </div>
            <div class="info-item">
              <label>Email Verified</label>
              <span>{{ $nurse->email_verified_at ? $nurse->email_verified_at->format('M d, Y') : 'Not verified' }}</span>
            </div>
          </div>
        </div>

        <!-- Activity Summary -->
        <div class="info-section">
          <h3><i class="fas fa-chart-line"></i> Activity Summary</h3>
          <div class="stats-grid">
            <div class="stat-item">
              <div class="stat-icon">
                <i class="fas fa-heartbeat"></i>
              </div>
              <div class="stat-info">
                <div class="stat-value">--</div>
                <div class="stat-label">Vitals Recorded</div>
              </div>
            </div>
            <div class="stat-item">
              <div class="stat-icon">
                <i class="fas fa-notes-medical"></i>
              </div>
              <div class="stat-info">
                <div class="stat-value">--</div>
                <div class="stat-label">Notes Added</div>
              </div>
            </div>
            <div class="stat-item">
              <div class="stat-icon">
                <i class="fas fa-user-injured"></i>
              </div>
              <div class="stat-info">
                <div class="stat-value">--</div>
                <div class="stat-label">Patients Assisted</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="quick-actions">
        <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
        <div class="action-buttons">
          <a href="{{ route('admin.nurses.edit', $nurse) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit Nurse
          </a>
          <form action="{{ route('admin.nurses.toggle-status', $nurse) }}" method="POST" style="display: inline;">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn {{ $nurse->is_active ? 'btn-secondary' : 'btn-success' }}">
              <i class="fas {{ $nurse->is_active ? 'fa-pause' : 'fa-play' }}"></i>
              {{ $nurse->is_active ? 'Deactivate' : 'Activate' }}
            </button>
          </form>
          <form action="{{ route('admin.nurses.destroy', $nurse) }}" method="POST" style="display: inline;" 
                onsubmit="return confirm('Are you sure you want to delete this nurse account? This action cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
              <i class="fas fa-trash"></i> Delete Account
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.nurse-profile-container {
  max-width: 1000px;
  margin: 0 auto;
  padding: 20px;
}

.profile-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  overflow: hidden;
}

.profile-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 30px;
  display: flex;
  align-items: center;
  gap: 30px;
}

.profile-avatar {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: rgba(255,255,255,0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 32px;
  flex-shrink: 0;
}

.profile-info {
  flex: 1;
}

.profile-info h2 {
  margin: 0 0 5px 0;
  font-size: 28px;
  font-weight: 700;
}

.profile-code {
  margin: 0 0 10px 0;
  opacity: 0.9;
  font-size: 14px;
}

.profile-status .badge {
  font-size: 12px;
  padding: 4px 12px;
}

.profile-actions {
  flex-shrink: 0;
}

.profile-content {
  padding: 30px;
}

.info-sections {
  display: flex;
  flex-direction: column;
  gap: 30px;
}

.info-section {
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 20px;
}

.info-section h3 {
  margin: 0 0 20px 0;
  font-size: 16px;
  font-weight: 600;
  color: #374151;
  display: flex;
  align-items: center;
  gap: 10px;
}

.info-section h3 i {
  color: #667eea;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.info-item.full-width {
  grid-column: 1 / -1;
}

.info-item label {
  font-size: 12px;
  color: #64748b;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.info-item span {
  font-size: 14px;
  color: #1e293b;
  font-weight: 500;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 20px;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 15px;
  padding: 15px;
  background: #f8fafc;
  border-radius: 8px;
}

.stat-icon {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 16px;
}

.stat-value {
  font-size: 20px;
  font-weight: 700;
  color: #1e293b;
}

.stat-label {
  font-size: 12px;
  color: #64748b;
  margin-top: 2px;
}

.quick-actions {
  border-top: 1px solid #e5e7eb;
  padding-top: 30px;
  margin-top: 30px;
}

.quick-actions h3 {
  margin: 0 0 20px 0;
  font-size: 16px;
  font-weight: 600;
  color: #374151;
  display: flex;
  align-items: center;
  gap: 10px;
}

.quick-actions h3 i {
  color: #667eea;
}

.action-buttons {
  display: flex;
  gap: 15px;
  flex-wrap: wrap;
}

.badge {
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  display: inline-block;
}

.badge-success {
  background: #dcfce7;
  color: #166534;
}

.badge-warning {
  background: #fef3c7;
  color: #92400e;
}

.badge-info {
  background: #dbeafe;
  color: #1e40af;
}

.btn {
  padding: 12px 24px;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.btn-warning {
  background: #f59e0b;
  color: white;
}

.btn-success {
  background: #10b981;
  color: white;
}

.btn-secondary {
  background: #64748b;
  color: white;
}

.btn-danger {
  background: #ef4444;
  color: white;
}

.btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

@media (max-width: 768px) {
  .profile-header {
    flex-direction: column;
    text-align: center;
    gap: 20px;
  }
  
  .info-grid {
    grid-template-columns: 1fr;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .action-buttons {
    flex-direction: column;
  }
  
  .profile-content {
    padding: 20px;
  }
}
</style>
@endsection
