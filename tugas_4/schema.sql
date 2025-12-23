CREATE DATABASE IF NOT EXISTS capstone_wisata;
USE capstone_wisata;

CREATE TABLE IF NOT EXISTS packages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(120) NOT NULL,
  description TEXT NOT NULL,
  location VARCHAR(120) NOT NULL,
  duration_days INT NOT NULL,
  base_price INT NOT NULL,
  image_path VARCHAR(255) NOT NULL,
  youtube_url VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO packages (title, description, location, duration_days, base_price, image_path, youtube_url)
VALUES
('Sanghyang Dora Sunrise', 'Trekking ringan dengan panorama matahari terbit, dilanjutkan sarapan kuliner lokal dan sesi dokumentasi udara.', 'Bukit Sanghyang Dora - Lewimunding', 2, 850000, 'assets/images/package-1.jpg', 'https://www.youtube.com/watch?v=ji2C-znVAMs'),
('Bukit Kabut Lewimunding', 'Eksplorasi alam berkabut, workshop foto drone, dan makan siang bersama UMKM kuliner setempat.', 'Lewimunding, Majalengka', 1, 650000, 'assets/images/package-2.jpg', 'https://www.youtube.com/watch?v=8hL6Jt4U9F0'),
('Camping Rimba Dora', 'Camping ground eksklusif, api unggun, live music akustik, serta paket dokumentasi lengkap.', 'Hutan Pinus Sanghyang Dora', 3, 1250000, 'assets/images/hero-1.jpg', 'https://www.youtube.com/watch?v=zEVnqNSEq0Y');

CREATE TABLE IF NOT EXISTS bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_name VARCHAR(120) NOT NULL,
  phone VARCHAR(60) NOT NULL,
  order_date DATE NOT NULL,
  start_time TIME NOT NULL,
  duration_days INT NOT NULL,
  participants INT NOT NULL,
  services VARCHAR(255) NOT NULL,
  package_price INT NOT NULL,
  total_amount INT NOT NULL,
  package_id INT NULL,
  note TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_package FOREIGN KEY (package_id) REFERENCES packages (id) ON DELETE SET NULL
);

INSERT INTO bookings (customer_name, phone, order_date, start_time, duration_days, participants, services, package_price, total_amount, package_id, note)
VALUES
('Ayu Melati', '081234567890', '2025-12-20', '06:00', 2, 4, 'penginapan,transportasi', 2200000, 17600000, 1, 'Request kamar dekat spot sunrise'),
('Fajar N.', '089876543210', '2025-12-22', '08:00', 1, 6, 'transportasi,makan', 1700000, 10200000, 2, 'Butuh menu vegetarian'),
('Rina Keluarga', '082233445566', '2025-12-24', '15:00', 3, 5, 'penginapan,transportasi,makan', 2700000, 40500000, 3, 'Tambahan guide lokal');
