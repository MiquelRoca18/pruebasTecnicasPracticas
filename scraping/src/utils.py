import json
import os
from datetime import datetime


def save_to_json(data: dict, filepath: str) -> None:
    os.makedirs(os.path.dirname(filepath), exist_ok=True)

    with open(filepath, "w", encoding="utf-8") as f:
        json.dump(data, f, ensure_ascii=False, indent=2)

    print(f"\n✅ Datos guardados en: {filepath}")


def build_output(main_books: list[dict], category_books: list[dict], category_name: str) -> dict:
    return {
        "metadata": {
            "extracted_at": datetime.utcnow().isoformat() + "Z",
            "source": "http://books.toscrape.com",
            "total_books": len(main_books) + len(category_books),
        },
        "main_page": {
            "count": len(main_books),
            "books": main_books,
        },
        "category": {
            "category_name": category_name,
            "count": len(category_books),
            "books": category_books,
        },
    }