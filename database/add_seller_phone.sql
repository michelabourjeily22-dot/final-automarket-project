-- Add seller_phone column to cars table
-- Run this SQL in phpMyAdmin or your MySQL client
-- If the column already exists, you'll get an error which you can ignore

ALTER TABLE cars ADD COLUMN seller_phone VARCHAR(30) NULL;


