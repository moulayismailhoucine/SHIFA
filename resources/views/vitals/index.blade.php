@extends('layouts.app')
@section('page-title', 'Vitals History - ' . $patient->name)

@section('content')
<div class="page-header">
  <h1><i class="fas fa-heartbeat"></i> Vitals History</h1>
  <div class="header-info">
    <p>Patient: <strong>{{ $patient->name }}</strong></p>
    <p>ID: {{ $patient->id }}</p>
    <a href="{{ route('vitals.create', $patient->id) }}" class="btn btn-primary">
      <i class="fas fa-plus"></i> Add New Vitals
    </a>
  </div>
</div>

<div class="vitals-history-container">
  @if($vitals->count() > 0)
    <div class="vitals-list">
      @foreach($vitals as $vital)
        @php
          $bpSeverity = \App\Services\AlertService::getBPSeverity($vital->blood_pressure_systolic, $vital->blood_pressure_diastolic);
          $hrSeverity = \App\Services\AlertService::getHeartRateSeverity($vital->heart_rate);
          $tempSeverity = \App\Services\AlertService::getTemperatureSeverity($vital->temperature);
          $o2Severity = \App\Services\AlertService::getOxygenSeverity($vital->oxygen_saturation);
          $gluSeverity = \App\Services\AlertService::getGlucoseSeverity($vital->glucose_level);
          $hasAnyCritical = collect([$bpSeverity, $hrSeverity, $tempSeverity, $o2Severity, $gluSeverity])->contains('critical');
          $hasAnyWarning = collect([$bpSeverity, $hrSeverity, $tempSeverity, $o2Severity, $gluSeverity])->contains('warning');
          $overallSeverity = $hasAnyCritical ? 'critical' : ($hasAnyWarning ? 'warning' : 'normal');
        @endphp
        <div class="vital-card {{ $overallSeverity }}">
          <div class="vital-header">
            <div class="vital-date">
              <i class="fas fa-calendar"></i>
              <span>{{ $vital->created_at->format('M d, Y - h:i A') }}</span>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
              <span class="severity-badge {{ $overallSeverity }}">{{ \App\Services\AlertService::getSeverityLabel($overallSeverity) }}</span>
              <div class="vital-nurse">
                <i class="fas fa-user-nurse"></i>
                <span>{{ $vital->nurse->name ?? 'Unknown' }}</span>
              </div>
            </div>
          </div>
          
          <div class="vital-metrics">
            <div class="metric-group">
              <div class="metric">
                <div class="metric-icon blood-pressure">
                  <i class="fas fa-heart"></i>
                </div>
                <div class="metric-info">
                  <div class="metric-value {{ $bpSeverity }}">{{ $vital->blood_pressure_systolic }}/{{ $vital->blood_pressure_diastolic }}</div>
                  <div class="metric-label">Blood Pressure <span class="badge-{{ $bpSeverity }}">{{ \App\Services\AlertService::getSeverityLabel($bpSeverity) }}</span></div>
                </div>
              </div>
              
              <div class="metric">
                <div class="metric-icon heart-rate">
                  <i class="fas fa-heartbeat"></i>
                </div>
                <div class="metric-info">
                  <div class="metric-value {{ $hrSeverity }}">{{ $vital->heart_rate }}</div>
                  <div class="metric-label">Heart Rate (bpm) <span class="badge-{{ $hrSeverity }}">{{ \App\Services\AlertService::getSeverityLabel($hrSeverity) }}</span></div>
                </div>
              </div>
              
              <div class="metric">
                <div class="metric-icon temperature">
                  <i class="fas fa-thermometer-half"></i>
                </div>
                <div class="metric-info">
                  <div class="metric-value {{ $tempSeverity }}">{{ $vital->temperature }}</div>
                  <div class="metric-label">Temperature (°C) <span class="badge-{{ $tempSeverity }}">{{ \App\Services\AlertService::getSeverityLabel($tempSeverity) }}</span></div>
                </div>
              </div>
              
              <div class="metric">
                <div class="metric-icon oxygen">
                  <i class="fas fa-lungs"></i>
                </div>
                <div class="metric-info">
                  <div class="metric-value {{ $o2Severity }}">{{ $vital->oxygen_saturation }}</div>
                  <div class="metric-label">Oxygen (%) <span class="badge-{{ $o2Severity }}">{{ \App\Services\AlertService::getSeverityLabel($o2Severity) }}</span></div>
                </div>
              </div>
            </div>
            
            @if($vital->glucose_level || $vital->weight)
              <div class="metric-group secondary">
                @if($vital->glucose_level)
                  <div class="metric">
                    <div class="metric-icon glucose">
                      <i class="fas fa-tint"></i>
                    </div>
                    <div class="metric-info">
                      <div class="metric-value {{ $gluSeverity }}">{{ $vital->glucose_level }}</div>
                      <div class="metric-label">Glucose (mg/dL) <span class="badge-{{ $gluSeverity }}">{{ \App\Services\AlertService::getSeverityLabel($gluSeverity) }}</span></div>
                    </div>
                  </div>
                @endif
                
                @if($vital->weight)
                  <div class="metric">
                    <div class="metric-icon weight">
                      <i class="fas fa-weight"></i>
                    </div>
                    <div class="metric-info">
                      <div class="metric-value">{{ $vital->weight }}</div>
                      <div class="metric-label">Weight (kg)</div>
                    </div>
                  </div>
                @endif
              </div>
            @endif
          </div>
          
          @if($vital->notes)
            <div class="vital-notes">
              <h4><i class="fas fa-notes-medical"></i> Notes</h4>
              <p>{{ $vital->notes }}</p>
            </div>
          @endif
        </div>
      @endforeach
    </div>
    
    <!-- Pagination -->
    <div class="pagination">
      {{ $vitals->links() }}
    </div>
  @else
    <div class="empty-state">
      <i class="fas fa-heartbeat"></i>
      <h3>No Vitals Recorded</h3>
      <p>No vital signs have been recorded for this patient yet.</p>
      <a href="{{ route('vitals.create', $patient->id) }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Record First Vitals
      </a>
    </div>
  @endif
</div>

<style>
.vitals-history-container {
  max-width: 1000px;
  margin: 0 auto;
  padding: 20px;
}

.vitals-list {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.vital-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  overflow: hidden;
  transition: transform 0.3s ease;
}

.vital-card:hover {
  transform: translateY(-2px);
}

.vital-card.critical { border: 2px solid #ef4444; }
.vital-card.warning { border: 2px solid #f59e0b; }

.severity-badge {
  padding: 2px 10px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 700;
}
.severity-badge.normal { background: #f0fdf4; color: #166534; }
.severity-badge.warning { background: #fffbeb; color: #92400e; }
.severity-badge.critical { background: #fef2f2; color: #b91c1c; }

.metric-value.critical { color: #ef4444; font-weight: 800; }
.metric-value.warning { color: #f59e0b; font-weight: 700; }

.badge-normal, .badge-success {
  padding: 1px 6px;
  border-radius: 10px;
  font-size: 10px;
  font-weight: 600;
  background: #f0fdf4;
  color: #166534;
  margin-left: 4px;
}
.badge-warning {
  padding: 1px 6px;
  border-radius: 10px;
  font-size: 10px;
  font-weight: 600;
  background: #fffbeb;
  color: #92400e;
  margin-left: 4px;
}
.badge-critical {
  padding: 1px 6px;
  border-radius: 10px;
  font-size: 10px;
  font-weight: 600;
  background: #fef2f2;
  color: #b91c1c;
  margin-left: 4px;
}

.vital-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 20px;
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
  border-bottom: 1px solid #e2e8f0;
}

.vital-date, .vital-nurse {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #64748b;
  font-size: 14px;
}

.vital-date i, .vital-nurse i {
  color: #667eea;
}

.vital-metrics {
  padding: 20px;
}

.metric-group {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 20px;
  margin-bottom: 20px;
}

.metric-group.secondary {
  margin-top: 15px;
  padding-top: 15px;
  border-top: 1px solid #f1f5f9;
}

.metric {
  display: flex;
  align-items: center;
  gap: 12px;
}

.metric-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 16px;
}

.metric-icon.blood-pressure {
  background: linear-gradient(135deg, #ef4444, #dc2626);
}

.metric-icon.heart-rate {
  background: linear-gradient(135deg, #f59e0b, #d97706);
}

.metric-icon.temperature {
  background: linear-gradient(135deg, #10b981, #059669);
}

.metric-icon.oxygen {
  background: linear-gradient(135deg, #3b82f6, #2563eb);
}

.metric-icon.glucose {
  background: linear-gradient(135deg, #8b5cf6, #7c3aed);
}

.metric-icon.weight {
  background: linear-gradient(135deg, #ec4899, #db2777);
}

.metric-info {
  flex: 1;
}

.metric-value {
  font-size: 18px;
  font-weight: 700;
  color: #1e293b;
  line-height: 1;
}

.metric-label {
  font-size: 12px;
  color: #64748b;
  margin-top: 2px;
}

.vital-notes {
  padding: 15px 20px;
  background: #f8fafc;
  border-top: 1px solid #e2e8f0;
}

.vital-notes h4 {
  margin: 0 0 10px 0;
  font-size: 14px;
  color: #475569;
  display: flex;
  align-items: center;
  gap: 8px;
}

.vital-notes h4 i {
  color: #667eea;
}

.vital-notes p {
  margin: 0;
  color: #64748b;
  font-size: 14px;
  line-height: 1.5;
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
  margin-top: 30px;
}

@media (max-width: 768px) {
  .vital-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }
  
  .metric-group {
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 15px;
  }
  
  .metric {
    flex-direction: column;
    text-align: center;
    gap: 8px;
  }
  
  .metric-icon {
    width: 35px;
    height: 35px;
    font-size: 14px;
  }
  
  .metric-value {
    font-size: 16px;
  }
}
</style>
@endsection
