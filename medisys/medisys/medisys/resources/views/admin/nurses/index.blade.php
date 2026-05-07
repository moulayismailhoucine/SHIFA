@extends('layouts.app')
@section('page-title', 'Nurse Management')

@section('content')
<div class="page-header">
  <h1><i class="fas fa-user-nurse"></i> Nurse Management</h1>
  <div class="header-actions">
    <a href="{{ route('admin.nurses.create') }}" class="btn btn-primary">
      <i class="fas fa-plus"></i> Add New Nurse
    </a>
  </div>
</div>

<div class="nurses-container">
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon blue"><i class="fas fa-user-nurse"></i></div>
      <div>
        <div class="stat-value">{{ $nurses->total() }}</div>
        <div class="stat-label">Total Nurses</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
      <div>
        <div class="stat-value">{{ $nurses->where('is_active', true)->count() }}</div>
        <div class="stat-label">Active Nurses</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon orange"><i class="fas fa-pause-circle"></i></div>
      <div>
        <div class="stat-value">{{ $nurses->where('is_active', false)->count() }}</div>
        <div class="stat-label">Inactive Nurses</div>
      </div>
    </div>
  </div>

  <div class="nurses-table-container">
    @if($nurses->count() > 0)
      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Username</th>
              <th>Department</th>
              <th>Status</th>
              <th>Created</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($nurses as $nurse)
              <tr>
                <td>
                  <div class="user-info">
                    <div class="user-avatar">
                      <i class="fas fa-user-nurse"></i>
                    </div>
                    <div>
                      <div class="user-name">{{ $nurse->name }}</div>
                      <div class="user-code">{{ $nurse->code }}</div>
                    </div>
                  </div>
                </td>
                <td>{{ $nurse->email }}</td>
                <td>{{ $nurse->username }}</td>
                <td>{{ $nurse->nurse?->department ?? 'Not assigned' }}</td>
                <td>
                  <span class="badge {{ $nurse->is_active ? 'badge-success' : 'badge-warning' }}">
                    {{ $nurse->is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td>{{ $nurse->created_at->format('M d, Y') }}</td>
                <td>
                  <div class="action-buttons">
                    <a href="{{ route('admin.nurses.show', $nurse) }}" class="btn btn-sm btn-info" title="View">
                      <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.nurses.edit', $nurse) }}" class="btn btn-sm btn-warning" title="Edit">
                      <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.nurses.destroy', $nurse) }}" method="POST" style="display: inline;" 
                          onsubmit="return confirm('Are you sure you want to delete this nurse account?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="pagination">
        {{ $nurses->links() }}
      </div>
    @else
      <div class="empty-state">
        <i class="fas fa-user-nurse"></i>
        <h3>No Nurses Found</h3>
        <p>No nurse accounts have been created yet.</p>
        <a href="{{ route('admin.nurses.create') }}" class="btn btn-primary">
          <i class="fas fa-plus"></i> Add First Nurse
        </a>
      </div>
    @endif
  </div>
</div>

<style>
.nurses-container {
  padding: 20px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.stat-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 15px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.stat-icon {
  width: 50px;
  height: 50px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 20px;
}

.stat-icon.blue { background: linear-gradient(135deg, #667eea, #764ba2); }
.stat-icon.green { background: linear-gradient(135deg, #10b981, #059669); }
.stat-icon.orange { background: linear-gradient(135deg, #f59e0b, #d97706); }

.stat-value {
  font-size: 24px;
  font-weight: 700;
  color: #1e293b;
}

.stat-label {
  font-size: 14px;
  color: #64748b;
  margin-top: 2px;
}

.nurses-table-container {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  overflow: hidden;
}

.table-responsive {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th {
  background: #f8fafc;
  padding: 12px 16px;
  text-align: left;
  font-size: 12px;
  font-weight: 600;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-bottom: 1px solid #e2e8f0;
}

.data-table td {
  padding: 16px;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea, #764ba2);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 14px;
}

.user-name {
  font-weight: 600;
  color: #1e293b;
}

.user-code {
  font-size: 12px;
  color: #64748b;
  margin-top: 2px;
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

.action-buttons {
  display: flex;
  gap: 8px;
  align-items: center;
}

.btn {
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  text-decoration: none;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  transition: all 0.2s ease;
}

.btn-sm {
  padding: 6px 10px;
  font-size: 12px;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
}

.btn-info {
  background: #3b82f6;
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
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: #64748b;
}

.empty-state i {
  font-size: 64px;
  margin-bottom: 20px;
  opacity: 0.5;
  color: #cbd5e1;
}

.empty-state h3 {
  margin: 0 0 10px 0;
  font-size: 24px;
  color: #475569;
}

.empty-state p {
  margin: 0 0 30px 0;
  font-size: 16px;
}

.pagination {
  display: flex;
  justify-content: center;
  padding: 20px;
  border-top: 1px solid #e2e8f0;
}

@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .action-buttons {
    flex-direction: column;
    gap: 4px;
  }
  
  .data-table {
    font-size: 14px;
  }
  
  .data-table th,
  .data-table td {
    padding: 8px;
  }
}
</style>
@endsection
