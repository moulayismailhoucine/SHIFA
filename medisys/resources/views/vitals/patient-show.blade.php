@extends('layouts.app')
@section('page-title', 'Patient Profile - ' . $patient->name)

@section('content')
<div class="page-header">
  <h1><i class="fas fa-user-injured"></i> Patient Profile</h1>
  <div class="header-actions">
    <a href="/patients" class="btn btn-secondary">
      <i class="fas fa-arrow-left"></i> Back to Patients
    </a>
    <a href="{{ route('vitals.create', $patient->id) }}" class="btn btn-primary">
      <i class="fas fa-plus"></i> Add Vitals
    </a>
  </div>
</div>

<div class="patient-profile-container">
  <!-- Patient Info Card -->
  <div class="profile-card">
    <div class="profile-header">
      <div class="patient-avatar-large">
        <i class="fas fa-user"></i>
      </div>
      <div class="profile-info">
        <h2>{{ $patient->name }}</h2>
        <div class="profile-meta">
          <span><i class="fas fa-id-card"></i> ID: {{ $patient->id }}</span>
          <span><i class="fas fa-birthday-cake"></i> Age: {{ $patient->age ?? 'N/A' }}</span>
          <span><i class="fas fa-venus-mars"></i> Gender: {{ ucfirst($patient->gender ?? 'N/A') }}</span>
          <span><i class="fas fa-tint"></i> Blood: {{ $patient->blood_type ?? 'N/A' }}</span>
        </div>
        <div class="profile-contact">
          <span><i class="fas fa-phone"></i> {{ $patient->phone ?? 'N/A' }}</span>
          <span><i class="fas fa-envelope"></i> {{ $patient->email ?? 'N/A' }}</span>
          @if($patient->nfc_uid)
            <span class="badge-nfc"><i class="fas fa-wifi"></i> NFC: {{ $patient->nfc_uid }}</span>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- Vitals Summary Card -->
  <div class="vitals-summary-card">
    <div class="card-header-section">
      <h3><i class="fas fa-heartbeat"></i> Vitals Summary</h3>
      <span class="vitals-count">{{ $vitalsCount }} total records</span>
    </div>

    @if($latestVitals->count() > 0)
      @php
        $latest = $latestVitals->first();
        $bpSev = \App\Services\AlertService::getBPSeverity($latest->blood_pressure_systolic, $latest->blood_pressure_diastolic);
        $hrSev = \App\Services\AlertService::getHeartRateSeverity($latest->heart_rate);
        $tempSev = \App\Services\AlertService::getTemperatureSeverity($latest->temperature);
        $o2Sev = \App\Services\AlertService::getOxygenSeverity($latest->oxygen_saturation);
        $gluSev = \App\Services\AlertService::getGlucoseSeverity($latest->glucose_level);
      @endphp
      <div class="latest-vitals-grid">
        <div class="latest-vital-item bp">
          <div class="vital-icon"><i class="fas fa-heart"></i></div>
          <div class="vital-data">
            <div class="vital-value {{ $bpSev }}">{{ $latest->blood_pressure_systolic }}/{{ $latest->blood_pressure_diastolic }}</div>
            <div class="vital-label">Blood Pressure <small>mmHg</small> <span class="badge-{{ $bpSev }}">{{ \App\Services\AlertService::getSeverityLabel($bpSev) }}</span></div>
          </div>
        </div>
        <div class="latest-vital-item hr">
          <div class="vital-icon"><i class="fas fa-heartbeat"></i></div>
          <div class="vital-data">
            <div class="vital-value {{ $hrSev }}">{{ $latest->heart_rate }}</div>
            <div class="vital-label">Heart Rate <small>bpm</small> <span class="badge-{{ $hrSev }}">{{ \App\Services\AlertService::getSeverityLabel($hrSev) }}</span></div>
          </div>
        </div>
        <div class="latest-vital-item temp">
          <div class="vital-icon"><i class="fas fa-thermometer-half"></i></div>
          <div class="vital-data">
            <div class="vital-value {{ $tempSev }}">{{ $latest->temperature }}°C</div>
            <div class="vital-label">Temperature <span class="badge-{{ $tempSev }}">{{ \App\Services\AlertService::getSeverityLabel($tempSev) }}</span></div>
          </div>
        </div>
        <div class="latest-vital-item o2">
          <div class="vital-icon"><i class="fas fa-lungs"></i></div>
          <div class="vital-data">
            <div class="vital-value {{ $o2Sev }}">{{ $latest->oxygen_saturation }}%</div>
            <div class="vital-label">Oxygen Saturation <span class="badge-{{ $o2Sev }}">{{ \App\Services\AlertService::getSeverityLabel($o2Sev) }}</span></div>
          </div>
        </div>
        @if($latest->glucose_level)
        <div class="latest-vital-item glucose">
          <div class="vital-icon"><i class="fas fa-tint"></i></div>
          <div class="vital-data">
            <div class="vital-value {{ $gluSev }}">{{ $latest->glucose_level }}</div>
            <div class="vital-label">Glucose <small>mg/dL</small> <span class="badge-{{ $gluSev }}">{{ \App\Services\AlertService::getSeverityLabel($gluSev) }}</span></div>
          </div>
        </div>
        @endif
        @if($latest->weight)
        <div class="latest-vital-item weight">
          <div class="vital-icon"><i class="fas fa-weight"></i></div>
          <div class="vital-data">
            <div class="vital-value">{{ $latest->weight }}</div>
            <div class="vital-label">Weight <small>kg</small></div>
          </div>
        </div>
        @endif
      </div>
      <div class="latest-vital-meta">
        <span>Recorded: {{ $latest->created_at->diffForHumans() }}</span>
        <span>By: {{ $latest->nurse->name ?? 'Unknown' }}</span>
      </div>
    @else
      <div class="empty-state-small">
        <i class="fas fa-heartbeat"></i>
        <p>No vitals recorded yet</p>
        <a href="{{ route('vitals.create', $patient->id) }}" class="btn btn-primary btn-sm">
          <i class="fas fa-plus"></i> Record First Vitals
        </a>
      </div>
    @endif
  </div>

  <!-- Vitals History -->
  <div class="vitals-history-section">
    <div class="section-header">
      <h3><i class="fas fa-history"></i> Vitals History</h3>
      <a href="{{ route('vitals.index', $patient->id) }}" class="btn btn-outline btn-sm">
        <i class="fas fa-list"></i> View All Records
      </a>
    </div>

    @if($latestVitals->count() > 0)
      <div class="vitals-table-wrapper">
        <table class="vitals-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Nurse</th>
              <th>BP</th>
              <th>HR</th>
              <th>Temp</th>
              <th>O2</th>
              <th>Glucose</th>
              <th>Weight</th>
              <th>Notes</th>
            </tr>
          </thead>
          <tbody>
            @foreach($latestVitals as $vital)
            @php
              $tBp = \App\Services\AlertService::getBPSeverity($vital->blood_pressure_systolic, $vital->blood_pressure_diastolic);
              $tHr = \App\Services\AlertService::getHeartRateSeverity($vital->heart_rate);
              $tTemp = \App\Services\AlertService::getTemperatureSeverity($vital->temperature);
              $tO2 = \App\Services\AlertService::getOxygenSeverity($vital->oxygen_saturation);
              $tGlu = \App\Services\AlertService::getGlucoseSeverity($vital->glucose_level);
            @endphp
            <tr>
              <td>{{ $vital->created_at->format('M d, Y H:i') }}</td>
              <td>{{ $vital->nurse->name ?? 'Unknown' }}</td>
              <td class="{{ $tBp }}">{{ $vital->blood_pressure_systolic }}/{{ $vital->blood_pressure_diastolic }}</td>
              <td class="{{ $tHr }}">{{ $vital->heart_rate }}</td>
              <td class="{{ $tTemp }}">{{ $vital->temperature }}°C</td>
              <td class="{{ $tO2 }}">{{ $vital->oxygen_saturation }}%</td>
              <td class="{{ $tGlu }}">{{ $vital->glucose_level ?? '-' }}</td>
              <td>{{ $vital->weight ?? '-' }}</td>
              <td><span class="notes-preview" title="{{ $vital->notes }}">{{ Str::limit($vital->notes, 30) ?: '-' }}</span></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @else
      <div class="empty-state-small">
        <i class="fas fa-clipboard-list"></i>
        <p>No vitals history available</p>
      </div>
    @endif
  </div>
</div>

<style>
.patient-profile-container {
  max-width: 1000px;
  margin: 0 auto;
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.profile-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  overflow: hidden;
}

.profile-header {
  display: flex;
  align-items: center;
  gap: 24px;
  padding: 30px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.patient-avatar-large {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: rgba(255,255,255,0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 36px;
  flex-shrink: 0;
}

.profile-info h2 {
  margin: 0 0 12px 0;
  font-size: 24px;
}

.profile-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 16px;
  margin-bottom: 12px;
  font-size: 14px;
  opacity: 0.9;
}

.profile-meta span {
  display: flex;
  align-items: center;
  gap: 6px;
}

.profile-contact {
  display: flex;
  flex-wrap: wrap;
  gap: 16px;
  font-size: 13px;
  opacity: 0.8;
}

.badge-nfc {
  background: rgba(255,255,255,0.2);
  padding: 2px 10px;
  border-radius: 20px;
  display: inline-flex;
  align-items: center;
  gap: 4px;
}

.vitals-summary-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  padding: 24px;
}

.card-header-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.card-header-section h3 {
  margin: 0;
  display: flex;
  align-items: center;
  gap: 10px;
  color: #374151;
}

.vitals-count {
  background: #f3f4f6;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  color: #6b7280;
}

.latest-vitals-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  gap: 16px;
  margin-bottom: 16px;
}

.latest-vital-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px;
  border-radius: 10px;
  background: #f8fafc;
}

.latest-vital-item.bp { border-left: 4px solid #ef4444; }
.latest-vital-item.hr { border-left: 4px solid #f59e0b; }
.latest-vital-item.temp { border-left: 4px solid #10b981; }
.latest-vital-item.o2 { border-left: 4px solid #3b82f6; }
.latest-vital-item.glucose { border-left: 4px solid #8b5cf6; }
.latest-vital-item.weight { border-left: 4px solid #ec4899; }

.vital-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  color: white;
}

.bp .vital-icon { background: linear-gradient(135deg, #ef4444, #dc2626); }
.hr .vital-icon { background: linear-gradient(135deg, #f59e0b, #d97706); }
.temp .vital-icon { background: linear-gradient(135deg, #10b981, #059669); }
.o2 .vital-icon { background: linear-gradient(135deg, #3b82f6, #2563eb); }
.glucose .vital-icon { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
.weight .vital-icon { background: linear-gradient(135deg, #ec4899, #db2777); }

.vital-value {
  font-size: 20px;
  font-weight: 700;
  color: #1e293b;
}

.vital-label {
  font-size: 12px;
  color: #64748b;
}

.vital-label small {
  font-size: 11px;
  opacity: 0.8;
}

.latest-vital-meta {
  display: flex;
  justify-content: space-between;
  padding-top: 12px;
  border-top: 1px solid #e5e7eb;
  font-size: 12px;
  color: #6b7280;
}

.vitals-history-section {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  padding: 24px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.section-header h3 {
  margin: 0;
  display: flex;
  align-items: center;
  gap: 10px;
  color: #374151;
}

.vitals-table-wrapper {
  overflow-x: auto;
}

.vitals-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13px;
}

.vitals-table th {
  text-align: left;
  padding: 12px;
  background: #f8fafc;
  color: #64748b;
  font-weight: 600;
  border-bottom: 2px solid #e2e8f0;
}

.vitals-table td {
  padding: 12px;
  border-bottom: 1px solid #f1f5f9;
  color: #374151;
}

.vitals-table tr:hover td {
  background: #f8fafc;
}

.vitals-table .highlight {
  font-weight: 700;
  color: #ef4444;
}

.notes-preview {
  color: #6b7280;
  cursor: help;
}

.empty-state-small {
  text-align: center;
  padding: 40px;
  color: #6b7280;
}

.empty-state-small i {
  font-size: 36px;
  margin-bottom: 10px;
  opacity: 0.5;
}

.empty-state-small p {
  margin: 0 0 16px 0;
}

.btn-outline {
  background: transparent;
  border: 2px solid #e5e7eb;
  color: #374151;
}

.btn-outline:hover {
  border-color: #667eea;
  color: #667eea;
}

.btn-sm {
  padding: 6px 14px;
  font-size: 12px;
}

/* Severity Badges */
.badge-success { padding: 1px 6px; border-radius: 10px; font-size: 10px; font-weight: 600; background: #f0fdf4; color: #166534; margin-left: 4px; }
.badge-warning { padding: 1px 6px; border-radius: 10px; font-size: 10px; font-weight: 600; background: #fffbeb; color: #92400e; margin-left: 4px; }
.badge-critical { padding: 1px 6px; border-radius: 10px; font-size: 10px; font-weight: 600; background: #fef2f2; color: #b91c1c; margin-left: 4px; }

/* Table abnormal value highlighting */
.vitals-table td.critical { color: #ef4444; font-weight: 700; }
.vitals-table td.warning { color: #f59e0b; font-weight: 600; }

/* Vital value coloring */
.vital-value.critical { color: #ef4444; font-weight: 800; }
.vital-value.warning { color: #f59e0b; font-weight: 700; }

@media (max-width: 768px) {
  .profile-header {
    flex-direction: column;
    text-align: center;
  }

  .latest-vitals-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .vitals-table {
    font-size: 12px;
  }
}
</style>
@endsection
