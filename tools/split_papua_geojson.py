import json
import os
import re

"""
Utility script to extract GeoJSON per kabupaten/kota for the 4 new Papua provinces
from `doc/38 Provinsi Indonesia - Kabupaten.json` and save them into `public/geojson`
with a filename pattern similar to the existing data.

USAGE (from project root):
    python tools/split_papua_geojson.py
"""

ROOT_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
SOURCE_PATH = os.path.join(ROOT_DIR, "doc", "38 Provinsi Indonesia - Kabupaten.json")
TARGET_DIR = os.path.join(ROOT_DIR, "public", "geojson")

# Target provinces (exactly as they appear in WADMPR)
TARGET_PROVINCES = {
    "Papua Tengah": "papua_tengah",
    "Papua Selatan": "papua_selatan",
    "Papua Pegunungan": "papua_pegunungan",
    "Papua Barat Daya": "papua_barat_daya",
    "Papua Barat": "papua_barat",
}


def slugify(name: str) -> str:
    """
    Convert a kabupaten/kota name to a filename-friendly slug.
    Example: 'Deiyai' -> 'Deiyai', 'Kota Sorong' -> 'Kota_Sorong'
    """
    name = name.strip()
    # Replace non-alphanumeric (except space) with space
    name = re.sub(r"[^0-9A-Za-z\s]", " ", name)
    # Collapse multiple spaces
    name = re.sub(r"\s+", " ", name)
    return name.replace(" ", "_")


def main():
    if not os.path.exists(SOURCE_PATH):
        raise SystemExit(f"SOURCE NOT FOUND: {SOURCE_PATH}")

    os.makedirs(TARGET_DIR, exist_ok=True)

    with open(SOURCE_PATH, "r", encoding="utf-8") as f:
        data = json.load(f)

    features = data.get("features", [])

    # Group features by (province, kabupaten/kota)
    groups = {}
    for feat in features:
        props = feat.get("properties", {}) or {}
        prov = props.get("WADMPR")
        kab = props.get("WADMKK")
        if not prov or not kab:
            continue
        if prov not in TARGET_PROVINCES:
            continue

        prov_slug = TARGET_PROVINCES[prov]
        kab_slug = slugify(kab)
        key = (prov, prov_slug, kab, kab_slug)
        groups.setdefault(key, []).append(feat)

    # Write per-kabupaten files
    for (prov, prov_slug, kab, kab_slug), feats in groups.items():
        filename = f"{prov_slug}_{kab_slug}.geojson"
        out_path = os.path.join(TARGET_DIR, filename)

        fc = {
            "type": "FeatureCollection",
            "name": f"{kab} - {prov}",
            "features": feats,
        }

        with open(out_path, "w", encoding="utf-8") as f:
            json.dump(fc, f, ensure_ascii=False)

        print(f"Written {out_path} ({len(feats)} features)")

    # Also write per-provinsi files (all kabupaten in that province)
    prov_groups = {}
    for (prov, prov_slug, kab, kab_slug), feats in groups.items():
        prov_groups.setdefault((prov, prov_slug), []).extend(feats)

    for (prov, prov_slug), feats in prov_groups.items():
        filename = f"{prov_slug}.geojson"
        out_path = os.path.join(TARGET_DIR, filename)
        fc = {
            "type": "FeatureCollection",
            "name": prov,
            "features": feats,
        }
        with open(out_path, "w", encoding="utf-8") as f:
            json.dump(fc, f, ensure_ascii=False)
        print(f"Written {out_path} (provinsi, {len(feats)} features)")


if __name__ == "__main__":
    main()


