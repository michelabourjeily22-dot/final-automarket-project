-- Seed data for Auto Marketplace
-- Sample car listings for testing

INSERT INTO cars (id, make, model, year, mileage, color, price) VALUES
('car1', 'Toyota', 'Camry', 2020, 25000, 'Silver', 25000.00),
('car2', 'Honda', 'Civic', 2019, 30000, 'Blue', 22000.00),
('car3', 'Ford', 'Mustang', 2021, 15000, 'Red', 35000.00);

INSERT INTO car_images (car_id, image_path, display_order) VALUES
('car1', 'uploads/sample1.jpg', 0),
('car2', 'uploads/sample2.jpg', 0),
('car3', 'uploads/sample3.jpg', 0);

