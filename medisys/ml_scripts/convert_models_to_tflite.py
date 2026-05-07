import tensorflow as tf
import zipfile, sys, os
from pathlib import Path
from datetime import datetime

root = Path(__file__).resolve().parent
models_dir = root / 'models'
if not models_dir.exists():
    print('MODELS_DIR_MISSING|'+str(models_dir))
    sys.exit(1)

models = sorted(models_dir.glob('*.h5'))
if not models:
    print('NO_H5_MODELS_FOUND')
    sys.exit(0)

# create zip backup containing only .h5 files
ts = datetime.now().strftime('%Y%m%d_%H%M%S')
zip_path = root / f'models_backup_{ts}.zip'
with zipfile.ZipFile(zip_path, 'w', compression=zipfile.ZIP_DEFLATED) as zf:
    for m in models:
        zf.write(m, arcname=m.name)
print(f'BACKUP_ZIP|{zip_path}|{zip_path.stat().st_size/1024/1024:.2f}MB')

# convert each model to tflite and quantized tflite
for m in models:
    name = m.stem
    try:
        print(f'LOADING|{m.name}')
        model = tf.keras.models.load_model(str(m))
        # regular conversion
        converter = tf.lite.TFLiteConverter.from_keras_model(model)
        tflite_model = converter.convert()
        tflite_path = m.with_suffix('.tflite')
        tflite_path.write_bytes(tflite_model)
        print(f'TFLITE_CONVERTED|{tflite_path.name}|{tflite_path.stat().st_size/1024/1024:.2f}MB')
        # quantized (dynamic range)
        converter = tf.lite.TFLiteConverter.from_keras_model(model)
        converter.optimizations = [tf.lite.Optimize.DEFAULT]
        tflite_q = converter.convert()
        qpath = m.with_name(name + '_quant.tflite')
        qpath.write_bytes(tflite_q)
        print(f'TFLITE_QUANT|{qpath.name}|{qpath.stat().st_size/1024/1024:.2f}MB')
    except Exception as e:
        print(f'ERROR|{m.name}|{e}')

print('CONVERSION_DONE')
