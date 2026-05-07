from fastapi import FastAPI, File, UploadFile, HTTPException
from fastapi.middleware.cors import CORSMiddleware
import numpy as np
from PIL import Image
import io
from utils import preprocess_image
import config
import logging

app = FastAPI()

# CORS للسماح لـ Laravel بالاتصال
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Try to import TensorFlow lazily so the API can start even if TF isn't installed
tf = None
try:
    import tensorflow as tf
except Exception:
    logging.exception("TensorFlow import failed; predictions will be unavailable")
    tf = None

# Load model if possible
model = None
model_loaded = False
if tf is not None:
    try:
        model = tf.keras.models.load_model(config.MODEL_PATH)
        model_loaded = True
    except Exception:
        logging.exception("Failed to load model; predictions will be unavailable")
        model = None
        model_loaded = False

@app.get("/")
def home():
    return {"status": "AI API is running", "model_loaded": model_loaded}

@app.post("/predict")
async def predict(file: UploadFile = File(...)):
    if not model_loaded or model is None:
        raise HTTPException(status_code=503, detail="Model not loaded")

    contents = await file.read()

    # تجهيز الصورة
    img = preprocess_image(contents, config.IMG_SIZE)

    # prediction
    prediction = model.predict(img)
    result = int(np.argmax(prediction))
    confidence = float(np.max(prediction))

    return {
        "prediction": result,
        "confidence": confidence
    }
