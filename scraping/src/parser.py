from bs4 import BeautifulSoup

STAR_RATING_MAP = {
    "one": 1,
    "two": 2,
    "three": 3,
    "four": 4,
    "five": 5,
}


def parse_star_rating(rating_class: str) -> int:
    return STAR_RATING_MAP.get(rating_class.lower(), 0)


def parse_price(price_text: str) -> float:
    cleaned = "".join(c for c in price_text if c.isdigit() or c == ".")
    return float(cleaned)


def parse_availability(availability_text: str) -> bool:
    return "in stock" in availability_text.strip().lower()

def parse_books_from_page(html: str) -> list[dict]:
    soup = BeautifulSoup(html, "html.parser")
    articles = soup.select("article.product_pod")
    books = []

    for article in articles:
        title = article.select_one("h3 > a")["title"]
        price = parse_price(article.select_one("p.price_color").text)
        available = parse_availability(article.select_one("p.availability").text)
        
        rating_classes = article.select_one("p.star-rating")["class"]
        rating = parse_star_rating(rating_classes[1])

        books.append({
            "title": title,
            "price": price,
            "available": available,
            "rating": rating,
        })

    return books