@extends('layouts.app')
@section('page-title', 'Patients')

@section('content')
<div class="page-header">
  <h1><i class="fas fa-user-injured"></i> Patients</h1>
  <div class="header-actions">
    <a href="{{ route('nurse.dashboard') }}" class="btn btn-secondary">
      <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>
  </div>
</div>

<div class="patients-container">
  <div class="patients-grid">
    @forelse($patients as $patient)
      <div class="patient-card">
        <div class="patient-header">
          <div class="patient-avatar">
            <i class="fas fa-user"></i>
          </div>
          <div class="patient-info">
            <h3>{{ $patient->name }}</h3>
            <p class="patient-id">ID: {{ $patient->id }}</p>
          </div>
        </div>
        
        <div class="patient-details">
          <div class="detail-row">
            <span class="label">Email:</span>
            <span class="value">{{ $patient->email ?? 'N/A' }}</span>
          </div>
          <div class="detail-row">
            <span class="label">Phone:</span>
            <span class="value">{{ $patient->phone ?? 'N/A' }}</span>
          </div>
          <div class="detail-row">
            <span class="label">Date of Birth:</span>
            <span class="value">{{ $patient->date_of_birth ? $patient->date_of_birth->format('M d, Y') : 'N/A' }}</span>
          </div>
        </div>

        <div class="patient-actions">
          <button class="btn btn-primary btn-sm" onclick="showVitalsModal({{ $patient->id }}, '{{ $patient->name }}')">
            <i class="fas fa-heartbeat"></i> Add Vitals
          </button>
          <button class="btn btn-secondary btn-sm" onclick="showNotesModal({{ $patient->id }}, '{{ $patient->name }}')">
            <i class="fas fa-notes-medical"></i> Add Notes
          </button>
        </div>
      </div>
    @empty
      <div class="empty-state">
        <i class="fas fa-user-injured"></i>
        <h3>No Patients Found</h3>
        <p>There are no patients in the system yet.</p>
      </div>
    @endforelse
  </div>

  <!-- Pagination -->
  @if($patients->hasPages())
    <div class="pagination">
      {{ $patients->links() }}
    </div>
  @endif
</div>

<!-- Vitals Modal -->
<div id="vitalsModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3><i class="fas fa-heartbeat"></i> Add Vitals</h3>
      <span class="close" onclick="closeModal('vitalsModal')">&times;</span>
    </div>
    <form id="vitalsForm" method="POST" action="">
      @csrf
      <input type="hidden" name="patient_id" id="vitalsPatientId">
      
      <div class="form-grid">
        <div class="form-group">
          <label>Blood Pressure (Systolic)</label>
          <input type="number" name="blood_pressure_systolic" required min="50" max="250" placeholder="120">
        </div>
        <div class="form-group">
          <label>Blood Pressure (Diastolic)</label>
          <input type="number" name="blood_pressure_diastolic" required min="30" max="150" placeholder="80">
        </div>
        <div class="form-group">
          <label>Heart Rate (bpm)</label>
          <input type="number" name="heart_rate" required min="40" max="200" placeholder="72">
        </div>
        <div class="form-group">
          <label>Temperature (°C)</label>
          <input type="number" step="0.1" name="temperature" required min="30" max="45" placeholder="36.5">
        </div>
        <div class="form-group">
          <label>Oxygen Saturation (%)</label>
          <input type="number" name="oxygen_saturation" required min="70" max="100" placeholder="98">
        </div>
        <div class="form-group">
          <label>Respiratory Rate</label>
          <input type="number" name="respiratory_rate" required min="8" max="40" placeholder="16">
        </div>
      </div>
      
      <div class="form-group">
        <label>Notes</label>
        <textarea name="notes" rows="3" placeholder="Additional observations..."></textarea>
      </div>
      
      <div class="form-actions">
        <button type="button" class="btn btn-secondary" onclick="closeModal('vitalsModal')">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Vitals</button>
      </div>
    </form>
  </div>
</div>

<!-- Notes Modal -->
<div id="notesModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3><i class="fas fa-notes-medical"></i> Add Nurse Note</h3>
      <span class="close" onclick="closeModal('notesModal')">&times;</span>
    </div>
    <form id="notesForm" method="POST" action="">
      @csrf
      <input type="hidden" name="patient_id" id="notesPatientId">
      
      <div class="form-group">
        <label>Note Type</label>
        <select name="type" required>
          <option value="">Select Type</option>
          <option value="observation">Observation</option>
          <option value="care">Care Provided</option>
          <option value="medication">Medication Related</option>
          <option value="other">Other</option>
        </select>
      </div>
      
      <div class="form-group">
        <label>Note</label>
        <textarea name="note" rows="5" required placeholder="Enter your nurse notes here..."></textarea>
      </div>
      
      <div class="form-actions">
        <button type="button" class="btn btn-secondary" onclick="closeModal('notesModal')">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Note</button>
      </div>
    </form>
  </div>
</div>

<style>
.patients-container {
  padding: 20px;
}

.patients-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.patient-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  overflow: hidden;
  transition: transform 0.3s ease;
}

.patient-card:hover {
  transform: translateY(-2px);
}

.patient-header {
  display: flex;
  align-items: center;
  padding: 20px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.patient-avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: rgba(255,255,255,0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 15px;
}

.patient-avatar i {
  font-size: 24px;
}

.patient-info h3 {
  margin: 0;
  font-size: 18px;
}

.patient-id {
  margin: 5px 0 0 0;
  opacity: 0.8;
  font-size: 14px;
}

.patient-details {
  padding: 20px;
}

.detail-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
  padding-bottom: 10px;
  border-bottom: 1px solid #f3f4f6;
}

.detail-row:last-child {
  border-bottom: none;
  margin-bottom: 0;
  padding-bottom: 0;
}

.label {
  font-weight: 600;
  color: #6b7280;
}

.value {
  color: #374151;
}

.patient-actions {
  padding: 20px;
  border-top: 1px solid #f3f4f6;
  display: flex;
  gap: 10px;
}

.modal {
  display: none;
  position: fixed;
  z-index: 1000;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.5);
}

.modal-content {
  background-color: white;
  margin: 5% auto;
  padding: 0;
  border-radius: 12px;
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 12px 12px 0 0;
}

.modal-header h3 {
  margin: 0;
  display: flex;
  align-items: center;
  gap: 10px;
}

.close {
  font-size: 28px;
  font-weight: bold;
  cursor: pointer;
  opacity: 0.8;
}

.close:hover {
  opacity: 1;
}

.modal-content form {
  padding: 20px;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 15px;
  margin-bottom: 20px;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: 600;
  color: #374151;
}

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 10px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-actions {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid #f3f4f6;
}

.empty-state {
  text-align: center;
  padding: 60px;
  color: #6b7280;
}

.empty-state i {
  font-size: 64px;
  margin-bottom: 20px;
  opacity: 0.5;
}

.pagination {
  display: flex;
  justify-content: center;
}

@media (max-width: 768px) {
  .patients-grid {
    grid-template-columns: 1fr;
  }
  
  .form-grid {
    grid-template-columns: 1fr;
  }
  
  .patient-actions {
    flex-direction: column;
  }
}
</style>

<script>
function showVitalsModal(patientId, patientName) {
  document.getElementById('vitalsPatientId').value = patientId;
  document.getElementById('vitalsForm').action = `/nurse/vitals/${patientId}`;
  document.getElementById('vitalsModal').style.display = 'block';
}

function showNotesModal(patientId, patientName) {
  document.getElementById('notesPatientId').value = patientId;
  document.getElementById('notesForm').action = `/nurse/notes/${patientId}`;
  document.getElementById('notesModal').style.display = 'block';
}

function closeModal(modalId) {
  document.getElementById(modalId).style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
  if (event.target.classList.contains('modal')) {
    event.target.style.display = 'none';
  }
}
</script>
@endsection
