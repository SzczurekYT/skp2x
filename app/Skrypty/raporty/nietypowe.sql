-- Raport - nietypowe karty
SET -- Ustawiamy zmienne
-- data od
@od = '2018-01-01 00:00:00',
-- data do
@do = '2018-06-30 23:59:00';

SELECT
cards.name AS 'Nazwa karty',
orders.nr AS 'Nr Handlowy',
cards.ksztalt AS 'Typowa?',
cards.chip AS "Rodzaj chop'a",
cards.ilosc AS 'Ilość',
cards.price AS 'Kwota jednostkowa',
cards.id,
cards.user_id,    orders.id, orders.data_publikacji, orders.stop_day
FROM
cards INNER JOIN orders ON cards.order_id=orders.id
WHERE (cards.ksztalt>0 OR cards.chip>0) AND orders.stop_day >= @od AND orders.stop_day < @do AND cards.user_id IN (2,11)
ORDER BY cards.user_id, orders.stop_day;