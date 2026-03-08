# Books Scraper

Extrae información de libros desde [books.toscrape.com](http://books.toscrape.com) y la guarda en JSON.

## Qué extrae

- 20 libros de la página principal
- 20 libros de una categoría específica

Por cada libro: título, precio (numérico), disponibilidad (booleano) y calificación (entero 1–5).

## Instalación
```bash
python3 -m venv .venv
source .venv/bin/activate
pip install -r requirements.txt
```

## Uso
```bash
python3 main.py
```

El resultado se guarda en `output/libros_extraidos.json`.

## Estructura
```
books-scraper/
├── src/
│   ├── parser.py     # Parseo del HTML y limpieza de datos
│   ├── scraper.py    # Peticiones HTTP y navegación
│   └── utils.py      # Guardado JSON
├── output/
│   └── libros_extraidos.json
├── main.py
└── requirements.txt
```