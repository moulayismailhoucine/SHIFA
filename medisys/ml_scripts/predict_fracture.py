import argparse
import json
import os
import sys

os.environ['TF_CPP_MIN_LOG_LEVEL'] = '3'

# Clinical notes per diagnosis
CLINICAL_NOTES = {
    "Fractured": (
        "Radiographic evidence suggests a potential bone fracture. "
        "Immediate clinical evaluation recommended. Consider immobilization, "
        "pain management, and orthopedic consult if fracture is confirmed."
    ),
    "Normal": (
        "No obvious fracture detected radiographically. "
        "Clinical correlation advised. If symptoms persist, follow-up imaging "
        "or CT scan may be warranted."
    ),
}

def predict(image_path, model_path):
    try:
        import numpy as np
        from PIL import Image
        from tensorflow.keras.models import load_model
        from tensorflow.keras.preprocessing.image import img_to_array
        
        model = load_model(model_path)
        img = Image.open(image_path).convert('RGB').resize((224, 224))
        img_array = img_to_array(img) / 255.0
        img_array = np.expand_dims(img_array, axis=0)
        prediction = model.predict(img_array, verbose=0)
        fracture_prob = float(prediction[0][0])
        diagnosis = "Fractured" if fracture_prob > 0.5 else "Normal"

        clinical_note = CLINICAL_NOTES.get(diagnosis, "")

        result = {
            "status": "success",
            "fracture_probability": fracture_prob,
            "diagnosis": diagnosis,
            "clinical_note": clinical_note,
        }

        print(json.dumps(result))
        sys.exit(0)

    except Exception as e:
        print(json.dumps({"status": "error", "message": str(e)}))
        sys.exit(1)


if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="X-Ray Bone Fracture Predictor using CNN")
    parser.add_argument("--image", required=True, help="Path to the X-ray image file")
    parser.add_argument("--model", required=True, help="Path to the trained .h5 model file")
    args = parser.parse_args()

    if not os.path.exists(args.image):
        print(json.dumps({"status": "error", "message": "Image not found"}))
        sys.exit(1)

    predict(args.image, args.model)
