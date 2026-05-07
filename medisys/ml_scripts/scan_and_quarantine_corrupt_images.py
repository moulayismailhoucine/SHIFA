#!/usr/bin/env python3
import os
import shutil
from PIL import Image
from PIL import ImageFile
ImageFile.LOAD_TRUNCATED_IMAGES = False

root = os.path.dirname(__file__)
# Common paths used by the fracture training script
candidates = [
    os.path.join(root, 'data_fracture', 'Bone_Fracture_Binary_Classification', 'Bone_Fracture_Binary_Classification'),
    os.path.join(root, 'data_fracture')
]

quarantine_root = os.path.join(root, 'data_fracture', 'quarantine_corrupt_images')
os.makedirs(quarantine_root, exist_ok=True)

moved = []
checked = 0

extensions = {'.jpg', '.jpeg', '.png', '.bmp', '.tif', '.tiff'}

for base in candidates:
    if not os.path.exists(base):
        continue
    for sub in ('train', 'val', 'test'):
        folder = os.path.join(base, sub)
        if not os.path.exists(folder):
            continue
        for dirpath, _, filenames in os.walk(folder):
            for fn in filenames:
                ext = os.path.splitext(fn)[1].lower()
                if ext not in extensions:
                    continue
                checked += 1
                src = os.path.join(dirpath, fn)
                try:
                    # First verify, then attempt to fully load the image (some truncation
                    # errors only surface on load/resize)
                    with Image.open(src) as img:
                        img.verify()
                    with Image.open(src) as img:
                        img.load()
                except Exception as e:
                    # move to quarantine preserving relative path
                    rel = os.path.relpath(src, base)
                    dest = os.path.join(quarantine_root, rel)
                    os.makedirs(os.path.dirname(dest), exist_ok=True)
                    try:
                        shutil.move(src, dest)
                        moved.append(dest)
                    except Exception as e2:
                        print(f"Failed to move corrupt file {src}: {e2}")

print(f"Checked {checked} image files.")
print(f"Quarantined {len(moved)} corrupt files (moved to {quarantine_root})")
if moved:
    for p in moved[:20]:
        print(p)

# If nothing was found, optionally try to open images (some issues are only detected on load)
if not moved:
    print("No corrupt files found by verify(). If training still errors, consider running with PIL ImageFile.LOAD_TRUNCATED_IMAGES = True or re-downloading dataset.")
