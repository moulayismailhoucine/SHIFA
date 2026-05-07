import os
import tensorflow as tf
from tensorflow.keras import layers, models
from tensorflow.keras.preprocessing.image import ImageDataGenerator
from tensorflow.keras.applications import MobileNetV2

"""
ISIC Archive Dataset CNN Training Script

Instructions to run locally:
1. Download dataset from ISIC Archive (https://www.isic-archive.com/)
2. Organize into directories:
   data/
    train/
      benign/
      malignant/
    validation/
      benign/
      malignant/
3. Run: python train_skin_cancer_cnn.py
"""

def create_model():
    # We use Transfer Learning (MobileNetV2) as a robust base for CNN
    base_model = MobileNetV2(input_shape=(224, 224, 3), include_top=False, weights='imagenet')
    base_model.trainable = False # Freeze base model

    model = models.Sequential([
        base_model,
        layers.GlobalAveragePooling2D(),
        layers.Dense(128, activation='relu'),
        layers.Dropout(0.5),
        layers.Dense(1, activation='sigmoid') # Sigmoid for binary classification (benign/malignant)
    ])

    model.compile(optimizer='adam',
                  loss='binary_crossentropy',
                  metrics=['accuracy'])
    return model

def train_model():
    # Default paths expected by the original script
    base_dir = 'data'

    # If the repo's downloaded ISIC dataset exists, point to it automatically
    repo_root = os.path.dirname(__file__)
    alt_dataset_dir = os.path.join(repo_root, 'data_isic2020', 'skin_dataset_resized')

    if os.path.exists(os.path.join(base_dir, 'train')) and os.path.exists(os.path.join(base_dir, 'validation')):
        train_dir = os.path.join(base_dir, 'train')
        val_dir = os.path.join(base_dir, 'validation')
    elif os.path.exists(os.path.join(alt_dataset_dir, 'train_set')) and os.path.exists(os.path.join(alt_dataset_dir, 'val_set')):
        train_dir = os.path.join(alt_dataset_dir, 'train_set')
        val_dir = os.path.join(alt_dataset_dir, 'val_set')
    else:
        # Fallback to original defaults (will raise later if missing)
        train_dir = os.path.join(base_dir, 'train')
        val_dir = os.path.join(base_dir, 'validation')

    # Data Augmentation to improve generalization
    train_datagen = ImageDataGenerator(
        rescale=1./255,
        rotation_range=20,
        width_shift_range=0.2,
        height_shift_range=0.2,
        horizontal_flip=True
    )

    val_datagen = ImageDataGenerator(rescale=1./255)

    train_generator = train_datagen.flow_from_directory(
        train_dir,
        target_size=(224, 224),
        batch_size=32,
        class_mode='binary'
    )

    validation_generator = val_datagen.flow_from_directory(
        val_dir,
        target_size=(224, 224),
        batch_size=32,
        class_mode='binary'
    )

    model = create_model()

    # Train the model
    # Allow overriding epochs with an environment variable for quick experiments
    epochs = int(os.environ.get('ISIC_EPOCHS', '5'))
    history = model.fit(
        train_generator,
        epochs=epochs,
        validation_data=validation_generator
    )

    # Save the trained model for the Laravel API to use
    os.makedirs('models', exist_ok=True)
    model.save('models/isic_cnn_model.h5')
    print("Model saved to models/isic_cnn_model.h5")

if __name__ == "__main__":
    print("Starting ISIC CNN Training...")
    train_model()
