@extends('layouts.app')
@section('page-title', 'Edit Nurse - ' . $nurse->name)

@section('content')
<div class="page-header">
  <h1><i class="fas fa-user-nurse"></i> Edit Nurse</h1>
  <div class="header-info">
    <p>Nurse: <strong>{{ $nurse->name }}</strong></p>
    <p>ID: {{ $nurse->code }}</p>
  </div>
  <div class="header-actions">
    <a href="{{ route('admin.nurses.index') }}" class="btn btn-secondary">
      <i class="fas fa-arrow-left"></i> Back to Nurses
    </a>
  </div>
</div>

<div class="nurse-form-container">
  <div class="form-card">
    <div class="form-header">
      <h3>Edit Nurse Information</h3>
      <p>Update nurse account details and settings</p>
    </div>

    <form method="POST" action="{{ route('admin.nurses.update', $nurse) }}" class="nurse-form">
      @csrf
      @method('PUT')
      
      <div class="form-section">
        <h4><i class="fas fa-user"></i> Basic Information</h4>
        <div class="form-grid">
          <div class="form-group">
            <label for="name">Full Name *</label>
            <input type="text" id="name" name="name" required value="{{ old('name', $nurse->name) }}" 
                   placeholder="Enter nurse's full name">
            @error('name')
              <span class="error-message">{{ $message }}</span>
            @enderror
          </div>

          <div class="form-group">
            <label for="email">Email Address *</label>
            <input type="email" id="email" name="email" required value="{{ old('email', $nurse->email) }}" 
                   placeholder="nurse@hospital.com">
            @error('email')
              <span class="error-message">{{ $message }}</span>
            @enderror
          </div>

          <div class="form-group">
            <label for="username">Username *</label>
            <input type="text" id="username" name="username" required value="{{ old('username', $nurse->username) }}" 
                   placeholder="nurse_username">
            @error('username')
              <span class="error-message">{{ $message }}</span>
            @enderror
          </div>

          <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" value="{{ old('phone', $nurse->phone) }}" 
                   placeholder="+213 5XXXXXXXX">
            @error('phone')
              <span class="error-message">{{ $message }}</span>
            @enderror
          </div>
        </div>
      </div>

      <div class="form-section">
        <h4><i class="fas fa-lock"></i> Security</h4>
        <div class="form-grid">
          <div class="form-group">
            <label for="password">New Password</label>
            <input type="password" id="password" name="password" 
                   placeholder="Leave blank to keep current password">
            @error('password')
              <span class="error-message">{{ $message }}</span>
            @enderror
            <small class="form-help">Enter new password only if you want to change it</small>
          </div>

          <div class="form-group">
            <label for="password_confirmation">Confirm New Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" 
                   placeholder="Re-enter new password">
            @error('password_confirmation')
              <span class="error-message">{{ $message }}</span>
            @enderror
          </div>
        </div>
      </div>

      <div class="form-section">
        <h4><i class="fas fa-hospital"></i> Professional Information</h4>
        <div class="form-grid">
          <div class="form-group">
            <label for="department">Department</label>
            <select id="department" name="department">
              <option value="">Select Department</option>
              <option value="Emergency" {{ old('department', $nurse->nurse?->department) == 'Emergency' ? 'selected' : '' }}>Emergency</option>
              <option value="ICU" {{ old('department', $nurse->nurse?->department) == 'ICU' ? 'selected' : '' }}>Intensive Care Unit (ICU)</option>
              <option value="Surgery" {{ old('department', $nurse->nurse?->department) == 'Surgery' ? 'selected' : '' }}>Surgery</option>
              <option value="Pediatrics" {{ old('department', $nurse->nurse?->department) == 'Pediatrics' ? 'selected' : '' }}>Pediatrics</option>
              <option value="Maternity" {{ old('department', $nurse->nurse?->department) == 'Maternity' ? 'selected' : '' }}>Maternity</option>
              <option value="Cardiology" {{ old('department', $nurse->nurse?->department) == 'Cardiology' ? 'selected' : '' }}>Cardiology</option>
              <option value="General" {{ old('department', $nurse->nurse?->department) == 'General' ? 'selected' : '' }}>General Ward</option>
            </select>
            @error('department')
              <span class="error-message">{{ $message }}</span>
            @enderror
          </div>

          <div class="form-group">
            <label for="license_number">License Number</label>
            <input type="text" id="license_number" name="license_number" value="{{ old('license_number', $nurse->nurse?->license_number) }}" 
                   placeholder="Professional license number">
            @error('license_number')
              <span class="error-message">{{ $message }}</span>
            @enderror
          </div>
        </div>
      </div>

      <div class="form-section">
        <h4><i class="fas fa-map-marker-alt"></i> Contact Information</h4>
        <div class="form-group full-width">
          <label for="address">Address</label>
          <textarea id="address" name="address" rows="3" placeholder="Enter full address">{{ old('address', $nurse->address) }}</textarea>
          @error('address')
            <span class="error-message">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <div class="form-section">
        <h4><i class="fas fa-toggle-on"></i> Account Status</h4>
        <div class="form-group">
          <label class="checkbox-label">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $nurse->is_active) ? 'checked' : '' }}>
            <span class="checkmark"></span>
            Account is active
          </label>
          <small class="form-help">Uncheck this to deactivate the nurse account</small>
        </div>
      </div>

      <div class="form-section account-info">
        <h4><i class="fas fa-info-circle"></i> Account Information</h4>
        <div class="info-grid">
          <div class="info-item">
            <label>Nurse ID:</label>
            <span>{{ $nurse->code }}</span>
          </div>
          <div class="info-item">
            <label>Created:</label>
            <span>{{ $nurse->created_at->format('M d, Y - h:i A') }}</span>
          </div>
          <div class="info-item">
            <label>Last Updated:</label>
            <span>{{ $nurse->updated_at->format('M d, Y - h:i A') }}</span>
          </div>
          <div class="info-item">
            <label>Current Status:</label>
            <span class="badge {{ $nurse->is_active ? 'badge-success' : 'badge-warning' }}">
              {{ $nurse->is_active ? 'Active' : 'Inactive' }}
            </span>
          </div>
        </div>
      </div>

      <div class="form-actions">
        <a href="{{ route('admin.nurses.show', $nurse) }}" class="btn btn-info">
          <i class="fas fa-eye"></i> View Nurse
        </a>
        <a href="{{ route('admin.nurses.index') }}" class="btn btn-secondary">
          <i class="fas fa-arrow-left"></i> Cancel
        </a>
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save"></i> Update Nurse Account
        </button>
      </div>
    </form>
  </div>
</div>

<style>
.nurse-form-container {
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
  padding: 30px;
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

.nurse-form {
  padding: 30px;
}

.form-section {
  margin-bottom: 40px;
}

.form-section h4 {
  margin: 0 0 20px 0;
  font-size: 16px;
  font-weight: 600;
  color: #374151;
  display: flex;
  align-items: center;
  gap: 10px;
  padding-bottom: 10px;
  border-bottom: 1px solid #e5e7eb;
}

.form-section h4 i {
  color: #667eea;
}

.form-section.account-info {
  background: #f8fafc;
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 30px;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 15px;
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: 5px;
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

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.form-group label {
  font-weight: 600;
  color: #374151;
  margin-bottom: 8px;
  display: block;
}

.form-group input,
.form-group select,
.form-group textarea {
  padding: 12px;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 14px;
  transition: all 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 12px;
  cursor: pointer;
  font-weight: 500;
  color: #374151;
}

.checkbox-label input[type="checkbox"] {
  width: 18px;
  height: 18px;
  margin: 0;
}

.form-help {
  color: #6b7280;
  font-size: 12px;
  margin-top: 5px;
  display: block;
}

.error-message {
  color: #ef4444;
  font-size: 12px;
  margin-top: 5px;
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

.form-actions {
  display: flex;
  gap: 15px;
  justify-content: flex-end;
  padding-top: 20px;
  border-top: 1px solid #e5e7eb;
  margin-top: 30px;
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

.btn-info {
  background: #3b82f6;
  color: white;
}

.btn-secondary {
  background: #f3f4f6;
  color: #374151;
}

.btn-secondary:hover {
  background: #e5e7eb;
}

@media (max-width: 768px) {
  .form-grid {
    grid-template-columns: 1fr;
  }
  
  .info-grid {
    grid-template-columns: 1fr;
  }
  
  .form-actions {
    flex-direction: column;
  }
  
  .nurse-form {
    padding: 20px;
  }
}
</style>
@endsection
