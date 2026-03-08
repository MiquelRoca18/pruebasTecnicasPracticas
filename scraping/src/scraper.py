import requests
from src.parser import parse_books_from_page

BASE_URL = "http://books.toscrape.com"

HEADERS = {
    "User-Agent": "Mozilla/5.0 (compatible; BooksScraper/1.0)"
}


def fetch_page(url: str) -> str:
    response = requests.get(url, headers=HEADERS, timeout=10)
    response.raise_for_status()
    return response.text


def scrape_books(start_url: str, limit: int = 20) -> list[dict]:
    books = []
    url = start_url

    while url and len(books) < limit:
        print(f"  → Fetching: {url}")
        html = fetch_page(url)
        books.extend(parse_books_from_page(html))

        if len(books) >= limit:
            break

        from bs4 import BeautifulSoup
        soup = BeautifulSoup(html, "html.parser")
        next_btn = soup.select_one("li.next > a")

        if next_btn:
            base_dir = url.rsplit("/", 1)[0]
            url = f"{base_dir}/{next_btn['href']}"
        else:
            url = None

    return books[:limit]

def get_category_url(category_name: str) -> str | None:
    from bs4 import BeautifulSoup
    html = fetch_page(BASE_URL)
    soup = BeautifulSoup(html, "html.parser")

    nav = soup.select_one("ul.nav.nav-list > li > ul")
    if not nav:
        return None

    for link in nav.find_all("a"):
        if link.text.strip().lower() == category_name.lower():
            return f"{BASE_URL}/{link['href']}"

    return None


def scrape_category(category_name: str, limit: int = 20) -> list[dict]:
    print(f"\n[Scraping] Categoría '{category_name}'")
    url = get_category_url(category_name)

    if not url:
        raise ValueError(f"Categoría '{category_name}' no encontrada.")

    return scrape_books(url, limit=limit)