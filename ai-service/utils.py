import numpy as np
from PIL import Image
import io

def preprocess_image(image_bytes, img_size):
    image = Image.open(io.BytesIO(image_bytes)).convert("RGB")
    image = image.resize((img_size, img_size))

    img = np.array(image) / 255.0
    img = np.expand_dims(img, axis=0)

    return img