@extends('layouts.app')
@section('page-title', 'Nurse Notes - ' . $patient->name)

@section('content')
<div class="page-header">
  <h1><i class="fas fa-notes-medical"></i> Nurse Notes</h1>
  <div class="header-info">
    <p>Patient: <strong>{{ $patient->name }}</strong></p>
    <a href="{{ route('patients.show', $patient->id) }}" class="btn btn-secondary">
      <i class="fas fa-arrow-left"></i> Back to Profile
    </a>
  </div>
</div>

<div class="notes-container">
  @if($notes->count() > 0)
    <div class="notes-list">
      @foreach($notes as $note)
        <div class="note-card">
          <div class="note-header">
            <div class="note-meta">
              <span class="note-nurse"><i class="fas fa-user-nurse"></i> {{ $note->nurse->name ?? 'Unknown' }}</span>
              <span class="note-type badge-{{ $note->type }}">{{ ucfirst($note->type) }}</span>
            </div>
            <span class="note-date">{{ $note->created_at->format('M d, Y - h:i A') }}</span>
          </div>
          <div class="note-body">
            <p>{{ $note->note }}</p>
          </div>
        </div>
      @endforeach
    </div>

    <div class="pagination">
      {{ $notes->links() }}
    </div>
  @else
    <div class="empty-state">
      <i class="fas fa-notes-medical"></i>
      <h3>No Notes</h3>
      <p>No nurse notes have been recorded for this patient yet.</p>
    </div>
  @endif
</div>

<style>
.notes-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.notes-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.note-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  overflow: hidden;
  border-left: 4px solid #667eea;
}

.note-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 20px;
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
}

.note-meta {
  display: flex;
  align-items: center;
  gap: 12px;
}

.note-nurse {
  font-weight: 600;
  color: #374151;
  display: flex;
  align-items: center;
  gap: 6px;
}

.note-nurse i {
  color: #667eea;
}

.note-type {
  padding: 2px 10px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 600;
}

.badge-observation { background: #f0fdf4; color: #166534; }
.badge-care { background: #eff6ff; color: #1e40af; }
.badge-medication { background: #faf5ff; color: #6b21a8; }
.badge-other { background: #f3f4f6; color: #374151; }

.note-date {
  font-size: 12px;
  color: #6b7280;
}

.note-body {
  padding: 20px;
  color: #374151;
  line-height: 1.6;
}

.note-body p {
  margin: 0;
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: #6b7280;
}

.empty-state i {
  font-size: 48px;
  margin-bottom: 16px;
  opacity: 0.5;
}

.pagination {
  display: flex;
  justify-content: center;
  margin-top: 24px;
}
</style>
@endsection
