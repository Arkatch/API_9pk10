# API_9pk10
Wprowadzenie
Jesteś kierowcą ciężarówki. Codziennie wozisz świeże bułki, wędliny itd. Za każdy przejazd otrzymujesz zapłatę. Chcesz napisać aplikację, która pomoże ci śledzić twoje przejazdy, ilość przejechanych kilometrów i zarobionych pieniędzy.

Zadanie
Stwórz API zgodnie z załączoną dokumentacją,
API może zostać stworzone z użyciem dowolnej technologii,
Użyj Gita do udokumentowania historii projektu,
Logowanie nie jest wymagane
Dokumentacja
Transit
Endpoint do dodawania przejazdu. Odległość między adresem początkowym a końcowym przeliczana jest automatycznie.

HTTP REQUEST POST http://example.com/transits

Parametry zapytania:

{
  "source_address":         "ul. Zakręt 8, Poznań",
  "destination_address": "Złota 44, Warszawa",
  "price":                          450,
  "date":                          "2018-03-15"
}
Get daily report
Endpoint zwraca ilość przejechanych kilometrów oraz zarobionych pieniędzy pomiędzy dwoma datami.

HTTP REQUEST GET http://example.com/reports/daily?start_date=YYYY-MM-DD&end_date=YYYY-MM_DD

Parametry zapytania:

{
  "start_date": "2018-01-20",
  "end_date":  "2018-01-25"
}
Parametry odpowiedzi (JSON):

{
  "total_distance": "90km",
  "total_price":      "115PLN"
}
Get monthly report
Endpoint zwraca ilość przejechanych kilometrów, średni dystans przejazdu oraz średnią zapłatę za przejazd dla każdego dnia w obecnym miesiącu (np. dla daty 5 marca zwraca dni 1-4 marca).

HTTP REQUEST GET http://example.com/reports/monthly

Parametry odpowiedzi:

[
  {
    "date":                "March, 1st",
    "total_distance": "240km",
    "avg_distance":   "70km",
    "avg_price":        "213.7PLN"
  },
  {
    "date":                "March, 2nd",
    "total_distance": "76km",
    "avg_distance":   "76km",
    "avg_price":        "90.3PLN"
  },
  <...>
]
Dodatkowe punkty za:
asynchroniczne obliczanie odległości pomiędzy dwoma punktami