@extends('layouts.app')
@section('page-title', 'Add New Nurse')

@section('content')
<div class="page-header">
  <h1><i class="fas fa-user-nurse"></i> Add New Nurse</h1>
  <div class="header-actions">
    <a href="{{ route('admin.nurses.index') }}" class="btn btn-secondary">
      <i class="fas fa-arrow-left"></i> Back to Nurses
    </a>
  </div>
</div>

<div class="nurse-form-container">
  <div class="form-card">
    <div class="form-header">
      <h3>Nurse Information</h3>
      <p>Create a new nurse account for the hospital management system</p>
    </div>

    <form method="POST" action="{{ route('admin.nurses.store') }}" class="nurse-form">
      @csrf
      
      <div class="form-section">
        <h4><i class="fas fa-user"></i> Basic Information</h4>
        <div class="form-grid">
          <div class="form-group">
            <label for="name">Full Name *</label>
            <input type="text" id="name" name="name" required value="{{ old('name') }}" 
                   placeholder="Enter nurse's full name">
            @error('name')
              <span class="error-message">{{ $message }}</span>
            @enderror
          </div>

          <div class="form-group">
            <label for="email">Email Address *</label>
            <input type="email" id="email" name="email" required value="{{ old('email') }}" 
                   placeholder="nurse@hospital.com">
            @error('email')
              <span class="error-message">{{ $message }}</span>
            @enderror
          </div>

          <div class="form-group">
            <label for="username">Username *</label>
            <input type="text" id="username" name="username" required value="{{ old('username') }}" 
                   placeholder="nurse_username">
            @error('username')
              <span class="error-message">{{ $message }}</span>
            @enderror
          </div>

          <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" 
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
            <label for="password">Password *</label>
            <input type="password" id="password" name="password" required 
                   placeholder="Minimum 8 characters">
            @error('password')
              <span class="error-message">{{ $message }}</span>
            @enderror
          </div>

          <div class="form-group">
            <label for="password_confirmation">Confirm Password *</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required 
                   placeholder="Re-enter password">
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
              <option value="Emergency" {{ old('department') == 'Emergency' ? 'selected' : '' }}>Emergency</option>
              <option value="ICU" {{ old('department') == 'ICU' ? 'selected' : '' }}>Intensive Care Unit (ICU)</option>
              <option value="Surgery" {{ old('department') == 'Surgery' ? 'selected' : '' }}>Surgery</option>
              <option value="Pediatrics" {{ old('department') == 'Pediatrics' ? 'selected' : '' }}>Pediatrics</option>
              <option value="Maternity" {{ old('department') == 'Maternity' ? 'selected' : '' }}>Maternity</option>
              <option value="Cardiology" {{ old('department') == 'Cardiology' ? 'selected' : '' }}>Cardiology</option>
              <option value="General" {{ old('department') == 'General' ? 'selected' : '' }}>General Ward</option>
            </select>
            @error('department')
              <span class="error-message">{{ $message }}</span>
            @enderror
          </div>

          <div class="form-group">
            <label for="license_number">License Number</label>
            <input type="text" id="license_number" name="license_number" value="{{ old('license_number') }}" 
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
          <textarea id="address" name="address" rows="3" placeholder="Enter full address">{{ old('address') }}</textarea>
          @error('address')
            <span class="error-message">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <div class="form-section">
        <h4><i class="fas fa-toggle-on"></i> Account Status</h4>
        <div class="form-group">
          <label class="checkbox-label">
            <input type="checkbox" name="is_active" value="1" checked>
            <span class="checkmark"></span>
            Activate account immediately
          </label>
          <small class="form-help">Uncheck this to create the account in inactive status</small>
        </div>
      </div>

      <div class="form-actions">
        <a href="{{ route('admin.nurses.index') }}" class="btn btn-secondary">
          <i class="fas fa-arrow-left"></i> Cancel
        </a>
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save"></i> Create Nurse Account
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

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
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
  
  .form-actions {
    flex-direction: column;
  }
  
  .nurse-form {
    padding: 20px;
  }
}
</style>
@endsection
