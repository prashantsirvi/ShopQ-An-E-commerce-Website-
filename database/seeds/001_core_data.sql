-- ShopQ Core Seed Data
-- Phase 3 — Roles, users, categories, brands, settings, coupons, banners

USE shopq_database;

INSERT INTO roles (id, name, slug, description) VALUES
(1, 'Super Admin', 'super_admin', 'Full system access'),
(2, 'Customer', 'customer', 'Standard shopper account'),
(3, 'Admin', 'admin', 'Back-office administrator');

INSERT INTO users (role_id, first_name, last_name, email, phone, password, is_active, email_verified_at) VALUES
(1, 'Super', 'Admin', 'superadmin@shopq.local', '9000000001', '$2y$10$/Agherj9nFCxuG1DdCji5u/4ehB1ToOPttk.CLEUrh2dC7MR8Gwl2', 1, NOW()),
(3, 'ShopQ', 'Admin', 'admin@shopq.local', '9000000002', '$2y$10$/Agherj9nFCxuG1DdCji5u/4ehB1ToOPttk.CLEUrh2dC7MR8Gwl2', 1, NOW()),
(2, 'Demo', 'Customer', 'customer@shopq.local', '9000000003', '$2y$10$/Agherj9nFCxuG1DdCji5u/4ehB1ToOPttk.CLEUrh2dC7MR8Gwl2', 1, NOW());

-- Default password for all seed accounts: Admin@123

INSERT INTO categories (id, name, slug, description, icon, sort_order) VALUES
(1, 'Electronics', 'electronics', 'Smartphones, laptops, audio and gadgets', 'fa-microchip', 1),
(2, 'Fashion', 'fashion', 'Clothing and apparel for men and women', 'fa-shirt', 2),
(3, 'Home & Kitchen', 'home-kitchen', 'Appliances, cookware and home essentials', 'fa-blender', 3),
(4, 'Books', 'books', 'Bestsellers, academic and lifestyle books', 'fa-book', 4),
(5, 'Sports & Fitness', 'sports-fitness', 'Equipment, activewear and fitness gear', 'fa-dumbbell', 5),
(6, 'Beauty', 'beauty', 'Skincare, makeup and personal care', 'fa-spa', 6),
(7, 'Toys & Games', 'toys-games', 'Kids toys, board games and puzzles', 'fa-puzzle-piece', 7),
(8, 'Grocery', 'grocery', 'Daily essentials and packaged foods', 'fa-basket-shopping', 8),
(9, 'Footwear', 'footwear', 'Sneakers, sandals and formal shoes', 'fa-shoe-prints', 9),
(10, 'Accessories', 'accessories', 'Bags, watches, belts and more', 'fa-glasses', 10);

INSERT INTO brands (id, name, slug) VALUES
(1, 'NovaTech', 'novatech'),
(2, 'UrbanFit', 'urbanfit'),
(3, 'HomeMate', 'homemate'),
(4, 'ReadWell', 'readwell'),
(5, 'ActivePro', 'activepro'),
(6, 'GlowCare', 'glowcare'),
(7, 'PlayJoy', 'playjoy'),
(8, 'FreshBasket', 'freshbasket'),
(9, 'StrideX', 'stridex'),
(10, 'Modish', 'modish');

INSERT INTO product_tags (name, slug) VALUES
('New Arrival', 'new-arrival'),
('Best Seller', 'best-seller'),
('Trending', 'trending'),
('Limited Edition', 'limited-edition'),
('Eco Friendly', 'eco-friendly');

INSERT INTO settings (setting_key, setting_value, setting_group) VALUES
('site_name', 'ShopQ', 'general'),
('site_tagline', 'Your modern multi-category shopping destination', 'general'),
('currency', 'INR', 'general'),
('currency_symbol', '₹', 'general'),
('tax_percent', '18', 'checkout'),
('shipping_flat_rate', '49', 'checkout'),
('free_shipping_min', '999', 'checkout'),
('support_email', 'support@shopq.local', 'contact'),
('support_phone', '1800-000-0000', 'contact'),
('upi_id', 'shopq@upi', 'payment');

INSERT INTO coupons (code, description, discount_type, discount_value, min_order_amount, max_discount_amount, usage_limit, is_active, expires_at) VALUES
('WELCOME10', '10% off for new customers', 'percent', 10.00, 499.00, 200.00, 1000, 1, DATE_ADD(NOW(), INTERVAL 1 YEAR)),
('FLAT100', 'Flat ₹100 off on orders above ₹999', 'fixed', 100.00, 999.00, NULL, 500, 1, DATE_ADD(NOW(), INTERVAL 6 MONTH)),
('MEGA20', '20% mega sale discount', 'percent', 20.00, 1499.00, 500.00, 200, 1, DATE_ADD(NOW(), INTERVAL 3 MONTH));

INSERT INTO banners (title, subtitle, image_path, link_url, placement, sort_order, is_active) VALUES
('Mega Summer Sale', 'Up to 50% off on top categories', 'uploads/banners/hero-summer-sale.jpg', '/deals', 'hero', 1, 1),
('New Tech Arrivals', 'Latest gadgets at best prices', 'uploads/banners/hero-tech.jpg', '/products', 'hero', 2, 1),
('Free Shipping', 'On orders above ₹999', 'uploads/banners/hero-shipping.jpg', '/products', 'hero', 3, 1);

INSERT INTO offers (title, slug, description, discount_percent, is_active, ends_at) VALUES
('Electronics Bonanza', 'electronics-bonanza', 'Extra savings on electronics this week', 15, 1, DATE_ADD(NOW(), INTERVAL 14 DAY)),
('Fashion Fiesta', 'fashion-fiesta', 'Trendy styles at unbeatable prices', 20, 1, DATE_ADD(NOW(), INTERVAL 10 DAY));

INSERT INTO flash_sales (title, slug, starts_at, ends_at, is_active) VALUES
('Flash Hour Deals', 'flash-hour-deals', NOW(), DATE_ADD(NOW(), INTERVAL 2 DAY), 1);

INSERT INTO memberships (name, slug, discount_percent, price, duration_days, benefits) VALUES
('ShopQ Silver', 'silver', 5, 499.00, 365, '5% extra discount, free shipping on select items'),
('ShopQ Gold', 'gold', 10, 999.00, 365, '10% extra discount, priority support, early sale access'),
('ShopQ Platinum', 'platinum', 15, 1499.00, 365, '15% extra discount, exclusive deals, dedicated support');

INSERT INTO wallets (user_id, balance) VALUES (3, 250.00);
