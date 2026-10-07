-- =====================================================================
-- Database Sistem Informasi Vika Jaya
-- Satu database dipakai Web (Owner) dan Mobile (Kasir) lewat REST API.
-- Target: MySQL 8 / MariaDB 10.4+ (XAMPP, Laragon). Engine InnoDB.
-- =====================================================================

CREATE DATABASE IF NOT EXISTS vika_jaya
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vika_jaya;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS pembayaran, detail_transaksi, transaksi,
  detail_distribusi, distribusi, stok_cabang, stok_pusat,
  detail_produksi, produksi, bahan, produk, users, cabang;
SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
-- MASTER
-- ---------------------------------------------------------------------
CREATE TABLE cabang (
  id_cabang   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nama_cabang VARCHAR(100) NOT NULL,
  alamat      VARCHAR(255) NULL,
  telepon     VARCHAR(20)  NULL,
  created_at  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id_cabang)
) ENGINE=InnoDB;

CREATE TABLE users (
  id_user    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_cabang  INT UNSIGNED NULL COMMENT 'NULL untuk Owner, wajib untuk Kasir',
  nama       VARCHAR(100) NOT NULL,
  username   VARCHAR(50)  NOT NULL,
  password   VARCHAR(255) NOT NULL COMMENT 'hash bcrypt',
  no_hp      VARCHAR(20)  NULL,
  role       ENUM('owner','kasir') NOT NULL,
  status     ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id_user),
  UNIQUE KEY uq_users_username (username),
  KEY idx_users_cabang (id_cabang),
  CONSTRAINT fk_users_cabang FOREIGN KEY (id_cabang)
    REFERENCES cabang (id_cabang) ON UPDATE RESTRICT ON DELETE RESTRICT,
  CONSTRAINT chk_kasir_cabang CHECK (role = 'owner' OR id_cabang IS NOT NULL)
) ENGINE=InnoDB;

CREATE TABLE produk (
  id_produk   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  kode_produk VARCHAR(30)  NOT NULL,
  nama_produk VARCHAR(100) NOT NULL,
  deskripsi   TEXT NULL,
  satuan      VARCHAR(20)  NOT NULL DEFAULT 'pcs',
  harga_jual  DECIMAL(12,2) NOT NULL DEFAULT 0,
  created_at  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id_produk),
  UNIQUE KEY uq_produk_kode (kode_produk),
  KEY idx_produk_nama (nama_produk)
) ENGINE=InnoDB;

CREATE TABLE bahan (
  id_bahan     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nama_bahan   VARCHAR(100) NOT NULL,
  satuan       VARCHAR(20)  NOT NULL,
  stok_bahan   DECIMAL(12,2) NOT NULL DEFAULT 0,
  stok_minimum DECIMAL(12,2) NOT NULL DEFAULT 0,
  created_at   TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id_bahan),
  KEY idx_bahan_nama (nama_bahan)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- PRODUKSI DAN STOK
-- ---------------------------------------------------------------------
CREATE TABLE produksi (
  id_produksi      INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_user          INT UNSIGNED NOT NULL,
  id_produk        INT UNSIGNED NOT NULL,
  tanggal_produksi DATE NOT NULL,
  jumlah_hasil     INT UNSIGNED NOT NULL,
  catatan          VARCHAR(255) NULL,
  created_at       TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_produksi),
  KEY idx_produksi_tanggal (tanggal_produksi),
  CONSTRAINT fk_produksi_user   FOREIGN KEY (id_user)   REFERENCES users (id_user)   ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_produksi_produk FOREIGN KEY (id_produk) REFERENCES produk (id_produk) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT chk_produksi_jumlah CHECK (jumlah_hasil > 0)
) ENGINE=InnoDB;

CREATE TABLE detail_produksi (
  id_detail_produksi INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_produksi        INT UNSIGNED NOT NULL,
  id_bahan           INT UNSIGNED NOT NULL,
  jumlah_digunakan   DECIMAL(12,2) NOT NULL,
  PRIMARY KEY (id_detail_produksi),
  KEY idx_dprod_produksi (id_produksi),
  CONSTRAINT fk_dprod_produksi FOREIGN KEY (id_produksi) REFERENCES produksi (id_produksi) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_dprod_bahan    FOREIGN KEY (id_bahan)    REFERENCES bahan (id_bahan)       ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT chk_dprod_jumlah CHECK (jumlah_digunakan > 0)
) ENGINE=InnoDB;

CREATE TABLE stok_pusat (
  id_stok_pusat INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_produk     INT UNSIGNED NOT NULL,
  jumlah        INT NOT NULL DEFAULT 0,
  stok_minimum  INT NOT NULL DEFAULT 0,
  updated_at    TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id_stok_pusat),
  UNIQUE KEY uq_stok_pusat_produk (id_produk),
  CONSTRAINT fk_stokpusat_produk FOREIGN KEY (id_produk) REFERENCES produk (id_produk) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT chk_stokpusat_jumlah CHECK (jumlah >= 0)
) ENGINE=InnoDB;

CREATE TABLE stok_cabang (
  id_stok_cabang INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_cabang      INT UNSIGNED NOT NULL,
  id_produk      INT UNSIGNED NOT NULL,
  jumlah         INT NOT NULL DEFAULT 0,
  updated_at     TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id_stok_cabang),
  UNIQUE KEY uq_stok_cabang (id_cabang, id_produk),
  CONSTRAINT fk_stokcabang_cabang FOREIGN KEY (id_cabang) REFERENCES cabang (id_cabang) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_stokcabang_produk FOREIGN KEY (id_produk) REFERENCES produk (id_produk) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT chk_stokcabang_jumlah CHECK (jumlah >= 0)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- DISTRIBUSI (pusat -> cabang)
-- ---------------------------------------------------------------------
CREATE TABLE distribusi (
  id_distribusi      INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_cabang          INT UNSIGNED NOT NULL,
  id_user            INT UNSIGNED NOT NULL,
  tanggal_distribusi DATE NOT NULL,
  catatan            VARCHAR(255) NULL,
  created_at         TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_distribusi),
  KEY idx_distribusi_tanggal (tanggal_distribusi),
  CONSTRAINT fk_distribusi_cabang FOREIGN KEY (id_cabang) REFERENCES cabang (id_cabang) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_distribusi_user   FOREIGN KEY (id_user)   REFERENCES users (id_user)    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE detail_distribusi (
  id_detail_distribusi INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_distribusi        INT UNSIGNED NOT NULL,
  id_produk            INT UNSIGNED NOT NULL,
  jumlah               INT UNSIGNED NOT NULL,
  PRIMARY KEY (id_detail_distribusi),
  KEY idx_ddist_distribusi (id_distribusi),
  CONSTRAINT fk_ddist_distribusi FOREIGN KEY (id_distribusi) REFERENCES distribusi (id_distribusi) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_ddist_produk     FOREIGN KEY (id_produk)     REFERENCES produk (id_produk)         ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT chk_ddist_jumlah CHECK (jumlah > 0)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- PENJUALAN (dibuat Kasir di Mobile, dilihat Owner di Web)
-- ---------------------------------------------------------------------
CREATE TABLE transaksi (
  id_transaksi      INT UNSIGNED NOT NULL AUTO_INCREMENT,
  no_transaksi      VARCHAR(30) NOT NULL COMMENT 'contoh: TRX-20261007-0001',
  id_cabang         INT UNSIGNED NOT NULL,
  id_user           INT UNSIGNED NOT NULL COMMENT 'kasir',
  tanggal_transaksi DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  total             DECIMAL(14,2) NOT NULL,
  status            ENUM('selesai','batal') NOT NULL DEFAULT 'selesai',
  created_at        TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_transaksi),
  UNIQUE KEY uq_transaksi_no (no_transaksi),
  KEY idx_transaksi_tanggal (tanggal_transaksi),
  KEY idx_transaksi_cabang (id_cabang, tanggal_transaksi),
  KEY idx_transaksi_kasir (id_user, tanggal_transaksi),
  CONSTRAINT fk_transaksi_cabang FOREIGN KEY (id_cabang) REFERENCES cabang (id_cabang) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_transaksi_user   FOREIGN KEY (id_user)   REFERENCES users (id_user)    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE detail_transaksi (
  id_detail_transaksi INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_transaksi        INT UNSIGNED NOT NULL,
  id_produk           INT UNSIGNED NOT NULL,
  jumlah              INT UNSIGNED NOT NULL,
  harga_satuan        DECIMAL(12,2) NOT NULL COMMENT 'harga saat transaksi',
  subtotal            DECIMAL(14,2) NOT NULL,
  PRIMARY KEY (id_detail_transaksi),
  KEY idx_dtrx_transaksi (id_transaksi),
  KEY idx_dtrx_produk (id_produk),
  CONSTRAINT fk_dtrx_transaksi FOREIGN KEY (id_transaksi) REFERENCES transaksi (id_transaksi) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_dtrx_produk    FOREIGN KEY (id_produk)    REFERENCES produk (id_produk)       ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT chk_dtrx_jumlah CHECK (jumlah > 0)
) ENGINE=InnoDB;

CREATE TABLE pembayaran (
  id_pembayaran INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_transaksi  INT UNSIGNED NOT NULL,
  metode        ENUM('tunai','transfer','qris') NOT NULL,
  uang_dibayar  DECIMAL(14,2) NOT NULL,
  kembalian     DECIMAL(14,2) NOT NULL DEFAULT 0,
  created_at    TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_pembayaran),
  UNIQUE KEY uq_pembayaran_transaksi (id_transaksi),
  CONSTRAINT fk_pembayaran_transaksi FOREIGN KEY (id_transaksi) REFERENCES transaksi (id_transaksi) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- VIEW untuk Dashboard, Penjualan, dan Laporan (tanpa tabel tambahan)
-- ---------------------------------------------------------------------
CREATE OR REPLACE VIEW v_penjualan AS
SELECT t.id_transaksi, t.no_transaksi, t.tanggal_transaksi, t.total, t.status,
       c.id_cabang, c.nama_cabang, u.id_user AS id_kasir, u.nama AS nama_kasir,
       p.metode, p.uang_dibayar, p.kembalian
FROM transaksi t
JOIN cabang c ON c.id_cabang = t.id_cabang
JOIN users u  ON u.id_user   = t.id_user
LEFT JOIN pembayaran p ON p.id_transaksi = t.id_transaksi;

CREATE OR REPLACE VIEW v_stok_menipis AS
SELECT pr.id_produk, pr.kode_produk, pr.nama_produk, sp.jumlah, sp.stok_minimum
FROM stok_pusat sp JOIN produk pr ON pr.id_produk = sp.id_produk
WHERE sp.jumlah <= sp.stok_minimum;

CREATE OR REPLACE VIEW v_omzet_harian AS
SELECT DATE(tanggal_transaksi) AS tanggal, id_cabang,
       COUNT(*) AS jumlah_transaksi, SUM(total) AS omzet
FROM transaksi WHERE status = 'selesai'
GROUP BY DATE(tanggal_transaksi), id_cabang;

-- ---------------------------------------------------------------------
-- DATA AWAL (contoh, ganti sesuai kebutuhan)
-- Password: owner -> owner123, kasir1 -> kasir123 (segera ganti setelah login)
-- ---------------------------------------------------------------------
INSERT INTO cabang (nama_cabang, alamat, telepon) VALUES
  ('Cabang 1', '[alamat cabang 1]', NULL),
  ('Cabang 2', '[alamat cabang 2]', NULL);

INSERT INTO users (id_cabang, nama, username, password, role) VALUES
  (NULL, 'Owner Vika Jaya', 'owner',  '$2b$10$esdCv9x0H3xNX1yr55P/a.5gT4fFKwXqerPO4V4PDDncDGMUQ1IvO', 'owner'),
  (1,    'Kasir Cabang 1',  'kasir1', '$2b$10$8mJUWUeuowAE.53mOfkGg.IZUSm.XxAPoAJS0rrLOyJlpJJjIP10e', 'kasir');

INSERT INTO produk (kode_produk, nama_produk, satuan, harga_jual) VALUES
  ('PRD-001', '[produk contoh]', 'pcs', 10000);
INSERT INTO stok_pusat (id_produk, jumlah, stok_minimum) VALUES (1, 0, 10);
INSERT INTO stok_cabang (id_cabang, id_produk, jumlah) VALUES (1, 1, 0), (2, 1, 0);
INSERT INTO bahan (nama_bahan, satuan, stok_bahan, stok_minimum) VALUES
  ('[bahan contoh]', 'kg', 0, 5);
