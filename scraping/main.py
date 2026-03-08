from src.scraper import scrape_books, scrape_category
from src.utils import build_output, save_to_json

OUTPUT_PATH = "output/libros_extraidos.json"
CATEGORY = "mystery"


def main():
    print("[1/3] Scraping página principal...")
    main_books = scrape_books("http://books.toscrape.com/index.html", limit=20)

    print("[2/3] Scraping categoría...")
    category_books = scrape_category(CATEGORY, limit=20)

    print("[3/3] Guardando JSON...")
    output = build_output(main_books, category_books, CATEGORY)
    save_to_json(output, OUTPUT_PATH)


if __name__ == "__main__":
    main()