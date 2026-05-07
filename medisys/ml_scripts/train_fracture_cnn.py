import os
import tensorflow as tf
from tensorflow.keras import layers, models
from tensorflow.keras.applications import MobileNetV2
from tensorflow.keras.preprocessing.image import ImageDataGenerator

"""
Bone Fracture Detection CNN Training Script
Dataset: Bone Fracture Multi-Region X-ray Data (Kaggle)
  https://www.kaggle.com/datasets/bmadushanirodrigo/fracture-multi-region-x-ray-data

Instructions:
1. Download dataset from Kaggle link above
2. Organize into:
   ml_scripts/data_fracture/
     train/
       fractured/
       not_fractured/
     validation/
       fractured/
       not_fractured/
3. Run: python train_fracture_cnn.py
"""

IMG_SIZE    = (224, 224)
BATCH_SIZE  = 32
EPOCHS      = 15
TRAIN_DIR   = 'data_fracture/Bone_Fracture_Binary_Classification/Bone_Fracture_Binary_Classification/train'
VAL_DIR     = 'data_fracture/Bone_Fracture_Binary_Classification/Bone_Fracture_Binary_Classification/val'
OUTPUT_PATH = 'models/fracture_cnn_model.h5'


def build_model():
    base = MobileNetV2(input_shape=(*IMG_SIZE, 3), include_top=False, weights='imagenet')
    base.trainable = False  # Freeze pre-trained weights

    model = models.Sequential([
        base,
        layers.GlobalAveragePooling2D(),
        layers.Dense(256, activation='relu'),
        layers.Dropout(0.4),
        layers.Dense(1, activation='sigmoid')  # Binary: fractured / not fractured
    ])

    model.compile(
        optimizer=tf.keras.optimizers.Adam(1e-4),
        loss='binary_crossentropy',
        metrics=['accuracy', tf.keras.metrics.AUC(name='auc')]
    )
    return model


def train():
    train_gen = ImageDataGenerator(
        rescale=1./255,
        rotation_range=15,
        width_shift_range=0.1,
        height_shift_range=0.1,
        horizontal_flip=True,
        zoom_range=0.1,
    )
    val_gen = ImageDataGenerator(rescale=1./255)

    train_ds = train_gen.flow_from_directory(
        TRAIN_DIR, target_size=IMG_SIZE, batch_size=BATCH_SIZE, class_mode='binary'
    )
    val_ds = val_gen.flow_from_directory(
        VAL_DIR, target_size=IMG_SIZE, batch_size=BATCH_SIZE, class_mode='binary'
    )

    model = build_model()
    model.summary()

    callbacks = [
        tf.keras.callbacks.EarlyStopping(patience=4, restore_best_weights=True),
        tf.keras.callbacks.ReduceLROnPlateau(factor=0.5, patience=2),
        tf.keras.callbacks.ModelCheckpoint(OUTPUT_PATH, save_best_only=True),
    ]

    # Allow overriding epochs with an environment variable for quicker experiments
    import os
    epochs = int(os.environ.get('FRAC_EPOCHS', str(EPOCHS)))

    history = model.fit(
        train_ds,
        epochs=epochs,
        validation_data=val_ds,
        callbacks=callbacks
    )

    os.makedirs('models', exist_ok=True)
    model.save(OUTPUT_PATH)
    print(f"\nModel saved to {OUTPUT_PATH}")

    val_loss, val_acc, val_auc = model.evaluate(val_ds)
    print(f"Validation Accuracy: {val_acc:.4f} | AUC: {val_auc:.4f}")


if __name__ == "__main__":
    print("Starting Bone Fracture CNN Training...")
    train()
