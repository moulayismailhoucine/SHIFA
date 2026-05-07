@extends('layouts.app')
@section('page-title', 'AI Skin Cancer Analysis')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-microscope"></i> AI Skin Cancer Analysis</h1>
    <p>Upload a skin lesion image to automatically detect cancer risk using Convolutional Neural Networks.</p>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h4 class="mb-4">Upload Image for Analysis</h4>

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form action="{{ route('skin-cancer.analyze') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Select Patient</label>
                        <select name="patient_id" class="form-control" required>
                            <option value="">-- Choose Patient --</option>
                            @foreach(\App\Models\Patient::all() as $patient)
                                <option value="{{ $patient->id }}">{{ $patient->user->name ?? 'Patient #'.$patient->id }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Skin Image (JPEG/PNG)</label>
                        <input type="file" name="image" class="form-control" accept="image/png, image/jpeg" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Clinical Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="E.g., Lesion found on left arm, growing for 2 months..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-brain"></i> Analyze Image
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if(session('result'))
    <div class="col-md-6">
        <div class="card shadow-sm border-0 border-left-primary">
            <div class="card-body text-center">
                <h4 class="text-success mb-3"><i class="fas fa-check-circle"></i> Analysis Complete</h4>
                
                <img src="{{ session('result')['image_url'] }}" alt="Analyzed Skin" class="img-thumbnail mb-4" style="max-height: 250px;">
                
                <div class="p-3 {{ session('result')['ai_analysis']['is_high_risk'] ? 'bg-danger text-white' : 'bg-light text-dark' }} rounded">
                    <h5>Diagnosis: <strong>{{ session('result')['ai_analysis']['diagnosis'] }}</strong></h5>
                    <h3>Risk Probability: {{ session('result')['ai_analysis']['risk_probability_percentage'] }}%</h3>
                </div>

                <p class="mt-3 text-muted small">This analysis has been automatically saved to the patient's Lab Results.</p>
                <a href="/lab-results" class="btn btn-outline-secondary btn-sm">View in Lab Results</a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
