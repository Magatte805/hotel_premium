
BEGIN;

TRUNCATE TABLE
  room_service,
  maintenance,
  reservation,
  room,
  service,
  users,
  hotel
RESTART IDENTITY
CASCADE;

-- Hotels
INSERT INTO hotel (id, name, city, address) VALUES
  (1, 'Hôtel Medina Palace', 'Dakar', '12 Avenue Habib Bourguiba, Dakar'),
  (2, 'Sahara Resort & Spa', ' Lille', 'Route de Nefta, Lille'),
  (3, 'Carthage Bay Hotel', 'Roubaix', 'Zone Touristique, Roubaix');

-- Rooms 
INSERT INTO room (id, number, price_per_night, hotel_id) VALUES
  (1, 101, 85,  1),
  (2, 102, 95,  1),
  (3, 201, 140, 1),
  (4, 110, 120, 2),
  (5, 210, 180, 2),
  (6, 301, 220, 2),
  (7, 10,  160, 3),
  (8, 11,  190, 3),
  (9, 12,  260, 3);

-- Services
INSERT INTO service (id, name, description, price) VALUES
  (1, 'Petit-déjeuner', 'Buffet continental (07:00–10:00)', 10),
  (2, 'Navette aéroport', 'Transfert aller simple', 25),
  (3, 'Spa', 'Accès spa (sauna + hammam) – 1h', 30),
  (4, 'Late checkout', 'Départ tardif (jusqu’à 14:00)', 15),
  (5, 'Room service', 'Service en chambre (frais)', 8);

INSERT INTO room_service (room_id, service_id) VALUES
  (1, 1), (1, 5),
  (2, 1), (2, 4),
  (3, 1), (3, 3), (3, 5),
  (4, 1), (4, 3),
  (5, 1), (5, 2), (5, 3),
  (6, 1), (6, 2), (6, 3), (6, 4),
  (7, 1),
  (8, 1), (8, 3),
  (9, 1), (9, 2), (9, 3);

-- Users

INSERT INTO users (id, email, last_name, first_name, phone, password, roles) VALUES
  (1, 'admin@hotel.tn',  'Hotel',  'Admin', '0000000000', '$2y$10$CRZnNCzbeUkGtQJayQ0hpeH6WrXfRqOJjHA5j6pJPeuKI.XzF2GOC', '["ROLE_ADMIN"]'::json),
  (2, 'client1@hotel.tn','Ben Ali','Sami',  '123456',     '$2y$10$C2BcRhnGAANIIzgkYZhoh.a.QQwXSuOGduxmb3dyIXLS4k3z7Wdsi', '["ROLE_CLIENT"]'::json),
  (3, 'client2@hotel.tn','Trabelsi','Nour','987654',     '$2y$10$C2BcRhnGAANIIzgkYZhoh.a.QQwXSuOGduxmb3dyIXLS4k3z7Wdsi', '["ROLE_CLIENT"]'::json);

-- Reservations
INSERT INTO reservation (id, start_date, end_date, status, room_id, user_id) VALUES
  (1, '2025-12-26 14:00:00', '2025-12-28 11:00:00', 'CONFIRMED',   1, 2),
  (2, '2026-01-03 14:00:00', '2026-01-06 11:00:00', 'IN_PROGRESS', 5, 3),
  (3, '2026-02-10 14:00:00', '2026-02-12 11:00:00', 'CANCELLED',   8, 2);

-- Maintenance
INSERT INTO maintenance (id, start_date, end_date_planned, end_date_real, status, description, room_id) VALUES
  (1, '2025-12-20 09:00:00', '2025-12-22 18:00:00', '2025-12-22 17:30:00', 'DONE',        'Remplacement climatisation', 3),
  (2, '2026-01-15 10:00:00', '2026-01-16 16:00:00', NULL,                  'PLANNED',     'Peinture + inspection électrique', 6),
  (3, '2026-01-20 08:30:00', '2026-01-20 12:00:00', NULL,                  'IN_PROGRESS', 'Réparation fuite salle de bain', 2);

SELECT setval('hotel_id_seq',       (SELECT COALESCE(MAX(id), 1) FROM hotel),       true);
SELECT setval('room_id_seq',        (SELECT COALESCE(MAX(id), 1) FROM room),        true);
SELECT setval('service_id_seq',     (SELECT COALESCE(MAX(id), 1) FROM service),     true);
SELECT setval('users_id_seq',       (SELECT COALESCE(MAX(id), 1) FROM users),       true);
SELECT setval('reservation_id_seq', (SELECT COALESCE(MAX(id), 1) FROM reservation), true);
SELECT setval('maintenance_id_seq', (SELECT COALESCE(MAX(id), 1) FROM maintenance), true);

COMMIT;




