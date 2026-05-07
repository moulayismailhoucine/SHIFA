# MediSys AI Diagnosis Microservice

## Overview
This FastAPI microservice powers the AI diagnosis tool for MediSys.
It supports two analysis types:
- **Skin lesion analysis** (dermatology) - based on ISIC Archive dataset patterns
- **Chest X-ray analysis** (radiology) - for pulmonary conditions

## Quick Start

```bash
cd ai-service

# Create virtual environment
python -m venv venv

# Activate (Windows)
venv\Scripts\activate

# Install dependencies
pip install -r requirements.txt

# Run the service (mock mode by default)
uvicorn main:app --host 127.0.0.1 --port 8001 --reload

# Test
http://127.0.0.1:8001/health
```

## Using Real Models

1. Train or download a TensorFlow/Keras model
2. Save as `ai-service/models/skin_model.h5` and/or `ai-service/models/xray_model.h5`
3. Install TensorFlow: `pip install tensorflow==2.16.1`
4. Run with real mode:
   ```bash
   set AI_USE_MOCK=false
   uvicorn main:app --host 127.0.0.1 --port 8001
   ```

## ISIC Archive Note
ISIC (International Skin Imaging Collaboration) provides 100,000+ dermoscopy images.
For training a real skin model:
- Download from https://challenge.isic-archive.com/
- Use transfer learning with EfficientNet-B0/B3
- Classes: Melanoma, Nevus, BCC, AK, etc.

## Datasets for Real Models
- **Skin**: ISIC Archive, HAM10000
- **X-ray**: ChestX-ray14 (NIH), MIMIC-CXR, VinDr-CXR
