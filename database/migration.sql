CREATE DATABASE IF NOT EXISTS cfms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cfms

CREATE TABLE roles (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  slug VARCHAR(40) NOT NULL UNIQUE,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE permissions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE role_permissions (
  role_id INT UNSIGNED NOT NULL,
  permission_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (role_id, permission_id),
  CONSTRAINT fk_rp_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
  CONSTRAINT fk_rp_permission FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(190) NOT NULL UNIQUE,
  name VARCHAR(120) NULL,
  role_id INT UNSIGNED NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  avatar_url VARCHAR(255) NULL,
  category VARCHAR(100) NULL,
  remember_token CHAR(64) NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  INDEX idx_users_role_active (role_id, is_active),
  INDEX idx_users_email (email),
  CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE counters (
  name VARCHAR(80) PRIMARY KEY,
  seq INT UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE branches (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL UNIQUE,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE feedback (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ticket_number INT UNSIGNED NOT NULL UNIQUE,
  name VARCHAR(120) NULL,
  email VARCHAR(190) NULL,
  phone VARCHAR(50) NULL,
  branch_id INT UNSIGNED NULL,
  category ENUM('Food Quality','Service','Ambiance','Cleanliness','Pricing','Menu','Value','Other') NOT NULL DEFAULT 'Other',
  type ENUM('compliment','suggestion','complaint') NOT NULL DEFAULT 'suggestion',
  rating TINYINT UNSIGNED NULL,
  message TEXT NOT NULL,
  status ENUM('pending','in-progress','resolved','escalated') NOT NULL DEFAULT 'pending',
  priority ENUM('low','medium','high') NOT NULL DEFAULT 'medium',
  assigned_to VARCHAR(190) NULL,
  staff_notes TEXT NULL,
  escalation_note TEXT NULL,
  response TEXT NULL,
  responded_at DATETIME NULL,
  resolved_at DATETIME NULL,
  automated_sms_at DATETIME NULL,
  automated_sms_body TEXT NULL,
  automated_sms_error VARCHAR(255) NULL,
  automated_sms_skipped ENUM('disabled','no_phone','invalid_phone','no_groq','complaint_policy') NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  INDEX idx_feedback_status (status),
  INDEX idx_feedback_category (category),
  INDEX idx_feedback_type (type),
  INDEX idx_feedback_priority (priority),
  INDEX idx_feedback_branch (branch_id),
  FULLTEXT INDEX ft_feedback_search (message, name, email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE feedback_categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE feedback_status (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(60) NOT NULL UNIQUE,
  slug VARCHAR(60) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE responses (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  feedback_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NULL,
  response TEXT NOT NULL,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_responses_feedback FOREIGN KEY (feedback_id) REFERENCES feedback(id) ON DELETE CASCADE,
  CONSTRAINT fk_responses_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE contacts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  phone VARCHAR(50) NOT NULL,
  email VARCHAR(190) NULL,
  notes TEXT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  INDEX idx_contacts_active (is_active),
  FULLTEXT INDEX ft_contacts_search (name, phone, email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE notifications (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NULL,
  title VARCHAR(160) NOT NULL,
  body TEXT NULL,
  type VARCHAR(60) NOT NULL DEFAULT 'system',
  read_at DATETIME NULL,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE audit_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NULL,
  action VARCHAR(80) NOT NULL,
  entity_type VARCHAR(80) NULL,
  entity_id INT UNSIGNED NULL,
  description VARCHAR(255) NULL,
  ip_address VARCHAR(64) NULL,
  user_agent VARCHAR(255) NULL,
  created_at DATETIME NOT NULL,
  INDEX idx_audit_created (created_at),
  CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE settings (
  setting_key VARCHAR(100) PRIMARY KEY,
  setting_value TEXT NULL,
  updated_at DATETIME NULL
) ENGINE=InnoDB;

CREATE TABLE password_resets (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(190) NOT NULL,
  token CHAR(64) NOT NULL,
  expires_at DATETIME NOT NULL,
  used_at DATETIME NULL,
  created_at DATETIME NOT NULL,
  INDEX idx_password_resets_email (email)
) ENGINE=InnoDB;

CREATE TABLE sessions (
  id VARCHAR(128) PRIMARY KEY,
  user_id INT UNSIGNED NULL,
  ip_address VARCHAR(64) NULL,
  user_agent VARCHAR(255) NULL,
  payload MEDIUMTEXT NULL,
  last_activity INT UNSIGNED NOT NULL,
  CONSTRAINT fk_sessions_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE attachments (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  feedback_id INT UNSIGNED NULL,
  user_id INT UNSIGNED NULL,
  original_name VARCHAR(255) NOT NULL,
  stored_name VARCHAR(255) NOT NULL,
  mime_type VARCHAR(120) NOT NULL,
  size_bytes INT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_attachments_feedback FOREIGN KEY (feedback_id) REFERENCES feedback(id) ON DELETE CASCADE,
  CONSTRAINT fk_attachments_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE menu_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  description TEXT NULL,
  price INT UNSIGNED NOT NULL DEFAULT 0,
  category VARCHAR(60) NOT NULL DEFAULT 'Other',
  image_url VARCHAR(255) NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE orders (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_number INT UNSIGNED NOT NULL UNIQUE,
  customer_name VARCHAR(120) NOT NULL,
  phone VARCHAR(50) NOT NULL,
  email VARCHAR(190) NULL,
  branch_id INT UNSIGNED NULL,
  order_type ENUM('delivery','pickup') NOT NULL DEFAULT 'delivery',
  delivery_address TEXT NULL,
  subtotal INT UNSIGNED NOT NULL DEFAULT 0,
  delivery_fee INT UNSIGNED NOT NULL DEFAULT 0,
  total INT UNSIGNED NOT NULL DEFAULT 0,
  status ENUM('pending','confirmed','preparing','ready','completed','cancelled') NOT NULL DEFAULT 'pending',
  payment_method ENUM('mtn_momo','airtel_money','cash') NOT NULL DEFAULT 'cash',
  payment_phone VARCHAR(50) NULL,
  notes TEXT NULL,
  items_json MEDIUMTEXT NULL,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_orders_branch FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
