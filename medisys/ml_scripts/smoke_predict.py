import sys, traceback
from pathlib import Path
import numpy as np
from PIL import Image
import tensorflow as tf

# Running from medisys/ml_scripts
script_root = Path(__file__).resolve().parent
# project root is two levels up from medisys/ml_scripts
project_root = script_root.parents[1]
model_path = project_root / 'medisys' / 'ml_scripts' / 'models' / 'isic_cnn_model.h5'
img_path = project_root / 'temp_test.png'

def preprocess_image(path, img_size=224):
    image = Image.open(path).convert('RGB')
    image = image.resize((img_size, img_size))
    arr = np.array(image) / 255.0
    arr = np.expand_dims(arr, axis=0)
    return arr

try:
    if not model_path.exists():
        print('MODEL_MISSING|' + str(model_path))
        sys.exit(2)
    print('LOADING_MODEL|' + str(model_path))
    model = tf.keras.models.load_model(str(model_path))
    print('MODEL_LOADED_OK')
    if not img_path.exists():
        print('IMAGE_MISSING|' + str(img_path))
        sys.exit(2)
    img = preprocess_image(img_path, 224)
    pred = model.predict(img)
    print('PRED_SHAPE|' + str(pred.shape))
    print('PRED_VALUES|' + str(pred.tolist()))
except Exception as e:
    traceback.print_exc()
    print('ERROR|' + str(e))
    sys.exit(3)
