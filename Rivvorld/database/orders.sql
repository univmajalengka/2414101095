CREATE TABLE orders (
  id_order INT AUTO_INCREMENT PRIMARY KEY,
  invoice_code VARCHAR(100) NOT NULL UNIQUE,
  id_produk INT NOT NULL,
  nama_produk VARCHAR(150) NOT NULL,
  deskripsi_produk TEXT,
  gambar_produk VARCHAR(255),
  jumlah INT NOT NULL,
  harga_satuan DECIMAL(10, 2) NOT NULL,
  total_harga DECIMAL(10, 2) NOT NULL,
  nama_pembeli VARCHAR(150) NOT NULL,
  email_pembeli VARCHAR(150),
  nomor_telepon VARCHAR(50),
  metode_pembayaran VARCHAR(50) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_orders_produk FOREIGN KEY (id_produk) REFERENCES produk(id_produk) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
