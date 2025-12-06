ALTER TABLE comments
  ADD COLUMN parent_id INT NULL AFTER comment_text,
  ADD INDEX idx_parent_id (parent_id);

ALTER TABLE comments
  ADD INDEX idx_comments_created_at (created_at);

CREATE TABLE IF NOT EXISTS comment_likes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  comment_id INT NOT NULL,
  user_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_user_comment_like (comment_id, user_id),
  INDEX idx_comment_id (comment_id),
  INDEX idx_user_id (user_id),
  CONSTRAINT fk_cl_comment FOREIGN KEY (comment_id) REFERENCES comments(id) ON DELETE CASCADE,
  CONSTRAINT fk_cl_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

