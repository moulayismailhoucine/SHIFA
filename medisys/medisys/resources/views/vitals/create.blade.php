@extends('layouts.app')
@section('page-title', 'Add Vitals - ' . $patient->name)

@section('content')
<div class="page-header">
  <h1><i class="fas fa-heartbeat"></i> Add Vitals</h1>
  <div class="header-info">
    <p>Patient: <strong>{{ $patient->name }}</strong></p>
    <p>ID: {{ $patient->id }}</p>
  </div>
</div>

<div class="vitals-form-container">
  <div class="form-card">
    <div class="form-header">
      <h3>Patient Vitals</h3>
      <p>Please enter the patient's current vital signs</p>
    </div>

    <form method="POST" action="{{ route('vitals.store', $patient->id) }}" class="vitals-form">
      @csrf
      
      <div class="form-grid">
        <!-- Blood Pressure -->
        <div class="form-group blood-pressure">
          <label><i class="fas fa-heart"></i> Blood Pressure</label>
          <div class="bp-inputs">
            <div class="bp-input-group">
              <input type="number" name="blood_pressure_systolic" required min="50" max="250" placeholder="120" value="{{ old('blood_pressure_systolic') }}">
              <span class="bp-label">Systolic</span>
            </div>
            <span class="bp-separator">/</span>
            <div class="bp-input-group">
              <input type="number" name="blood_pressure_diastolic" required min="30" max="150" placeholder="80" value="{{ old('blood_pressure_diastolic') }}">
              <span class="bp-label">Diastolic</span>
            </div>
            <span class="bp-unit">mmHg</span>
          </div>
          @error('blood_pressure_systolic')
            <span class="error-message">{{ $message }}</span>
          @enderror
          @error('blood_pressure_diastolic')
            <span class="error-message">{{ $message }}</span>
          @enderror
        </div>

        <!-- Heart Rate -->
        <div class="form-group">
          <label for="heart_rate"><i class="fas fa-heartbeat"></i> Heart Rate</label>
          <div class="input-with-unit">
            <input type="number" id="heart_rate" name="heart_rate" required min="40" max="200" placeholder="72" value="{{ old('heart_rate') }}">
            <span class="unit">bpm</span>
          </div>
          @error('heart_rate')
            <span class="error-message">{{ $message }}</span>
          @enderror
        </div>

        <!-- Temperature -->
        <div class="form-group">
          <label for="temperature"><i class="fas fa-thermometer-half"></i> Temperature</label>
          <div class="input-with-unit">
            <input type="number" id="temperature" name="temperature" required min="30" max="45" step="0.1" placeholder="36.5" value="{{ old('temperature') }}">
            <span class="unit">°C</span>
          </div>
          @error('temperature')
            <span class="error-message">{{ $message }}</span>
          @enderror
        </div>

        <!-- Oxygen Saturation -->
        <div class="form-group">
          <label for="oxygen_saturation"><i class="fas fa-lungs"></i> Oxygen Saturation</label>
          <div class="input-with-unit">
            <input type="number" id="oxygen_saturation" name="oxygen_saturation" required min="70" max="100" placeholder="98" value="{{ old('oxygen_saturation') }}">
            <span class="unit">%</span>
          </div>
          @error('oxygen_saturation')
            <span class="error-message">{{ $message }}</span>
          @enderror
        </div>

        <!-- Glucose Level -->
        <div class="form-group">
          <label for="glucose_level"><i class="fas fa-tint"></i> Glucose Level</label>
          <div class="input-with-unit">
            <input type="number" id="glucose_level" name="glucose_level" min="20" max="600" step="0.1" placeholder="90" value="{{ old('glucose_level') }}">
            <span class="unit">mg/dL</span>
          </div>
          <small class="form-help">Optional: Leave empty if not measured</small>
          @error('glucose_level')
            <span class="error-message">{{ $message }}</span>
          @enderror
        </div>

        <!-- Weight -->
        <div class="form-group">
          <label for="weight"><i class="fas fa-weight"></i> Weight</label>
          <div class="input-with-unit">
            <input type="number" id="weight" name="weight" min="1" max="500" step="0.1" placeholder="70" value="{{ old('weight') }}">
            <span class="unit">kg</span>
          </div>
          <small class="form-help">Optional: Leave empty if not measured</small>
          @error('weight')
            <span class="error-message">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <!-- Notes -->
      <div class="form-group full-width">
        <label for="notes"><i class="fas fa-notes-medical"></i> Notes</label>
        <textarea id="notes" name="notes" rows="4" placeholder="Additional observations, patient condition, medications taken, etc...">{{ old('notes') }}</textarea>
        <small class="form-help">Optional: Add any relevant observations or notes</small>
        @error('notes')
          <span class="error-message">{{ $message }}</span>
        @enderror
      </div>

      <!-- Form Actions -->
      <div class="form-actions">
        <a href="{{ route('patients.show', $patient->id) }}" class="btn btn-secondary">
          <i class="fas fa-arrow-left"></i> Cancel
        </a>
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save"></i> Save Vitals
        </button>
      </div>
    </form>
  </div>
</div>

<style>
.vitals-form-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.form-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  overflow: hidden;
}

.form-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 20px;
  text-align: center;
}

.form-header h3 {
  margin: 0 0 10px 0;
  font-size: 24px;
}

.form-header p {
  margin: 0;
  opacity: 0.9;
}

.vitals-form {
  padding: 30px;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 25px;
  margin-bottom: 30px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  font-weight: 600;
  color: #374151;
  margin-bottom: 8px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.form-group label i {
  color: #667eea;
  width: 16px;
}

.blood-pressure {
  grid-column: span 2;
}

.bp-inputs {
  display: flex;
  align-items: center;
  gap: 10px;
}

.bp-input-group {
  display: flex;
  flex-direction: column;
  flex: 1;
}

.bp-input-group input {
  padding: 12px;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 16px;
  text-align: center;
  font-weight: 600;
}

.bp-label {
  font-size: 12px;
  color: #6b7280;
  margin-top: 5px;
  text-align: center;
}

.bp-separator {
  font-size: 24px;
  font-weight: bold;
  color: #6b7280;
  margin-top: -10px;
}

.bp-unit {
  font-size: 14px;
  color: #6b7280;
  margin-left: 5px;
}

.input-with-unit {
  position: relative;
  display: flex;
  align-items: center;
}

.input-with-unit input {
  flex: 1;
  padding: 12px 50px 12px 12px;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 16px;
}

.input-with-unit .unit {
  position: absolute;
  right: 12px;
  color: #6b7280;
  font-weight: 600;
  font-size: 14px;
}

.full-width {
  grid-column: 1 / -1;
}

textarea {
  padding: 12px;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 14px;
  resize: vertical;
  font-family: inherit;
}

.form-help {
  color: #6b7280;
  font-size: 12px;
  margin-top: 5px;
}

.error-message {
  color: #ef4444;
  font-size: 12px;
  margin-top: 5px;
}

.form-actions {
  display: flex;
  gap: 15px;
  justify-content: flex-end;
  padding-top: 20px;
  border-top: 1px solid #e5e7eb;
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

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.btn-secondary {
  background: #f3f4f6;
  color: #374151;
}

.btn-secondary:hover {
  background: #e5e7eb;
}

input:focus, textarea:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

@media (max-width: 768px) {
  .form-grid {
    grid-template-columns: 1fr;
  }
  
  .blood-pressure {
    grid-column: span 1;
  }
  
  .bp-inputs {
    flex-direction: column;
    align-items: stretch;
  }
  
  .bp-separator {
    display: none;
  }
  
  .bp-unit {
    margin-left: 0;
    margin-top: 5px;
  }
  
  .form-actions {
    flex-direction: column;
  }
}
</style>
@endsection
