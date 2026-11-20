USE deskshop;

INSERT IGNORE INTO user (username, email, contact_number, password, role, status) VALUES
('Admin', 'demo.admin@example.com', '0770000000', 'DemoAdmin#2026', 'admin', 'active'),
('Test Customer', 'demo.customer@example.com', '0771111111', 'DemoCust#2026', 'customer', 'active');

INSERT IGNORE INTO categories (category_name) VALUES
('school-items'),('office-items'),('books'),('writing-tools'),('colour-products'),('desk-accessories');

INSERT IGNORE INTO products (product_code, name, price, stock, image_url) VALUES
('P001','Atlas A4 Black Drawing Book Small 40Pgs',350.00,50,'Assets/Product Page/Atlas A4 Black Drawing Book Small 40Pgs.png'),
('P002','Atlas A4 CR Book Single Ruled 120Pgs',420.00,80,'Assets/Product Page/Atlas A4 CR Book Single Ruled 120Pgs.png'),
('P003','Atlas A4 CR Book Square Ruled 160Pgs',520.00,60,'Assets/Product Page/Atlas A4 CR Book Square Ruled 160Pgs.png'),
('P004','Atlas A5 Practical Book Blank 80Pgs',280.00,70,'Assets/Product Page/Atlas A5 Practical Book Blank 80Pgs.png'),
('P005','Atlas Binder Glue Bottle 100g',150.00,40,'Assets/Product Page/Atlas Binder Glue Bottle 100g.png'),
('P006','Atlas Finelighter Green',120.00,100,'Assets/Product Page/Atlas Finelighter Green.png'),
('P007','Atlas Highlighter Green',130.00,100,'Assets/Product Page/Atlas Highlighter Green.png'),
('P008','Atlas Mathematical Box',650.00,30,'Assets/Product Page/Atlas Mathematical Box.jpg'),
('P009','Atlas Eraser',50.00,200,'Assets/Product Page/Atlas-eraser.png'),
('P010','Atlas Eraser Large',80.00,150,'Assets/Product Page/atlas-eraser-1.png'),
('P011','Atlas Sunflower Yellow HB Pencil 12 Pack',360.00,90,'Assets/Product Page/ATLAS-SUNFLOWER-YELOW-HB-PENCIL-12PACK.jpg'),
('P012','Homerun Colour Pencil 12 Colours',450.00,60,'Assets/Product Page/Homerun Colour Pencil 12 Colours.jpg'),
('P013','Mango Plastic Ruler 15cm',60.00,120,'Assets/Product Page/Mango Plastic Ruler 15cm.png'),
('P014','Mango Stapler Machine',480.00,35,'Assets/Product Page/mango Stapler Machine.png'),
('P015','Mango Puncher M480',550.00,25,'Assets/Product Page/mango-puncher-m480.png'),
('P016','Maped Color Peps Star 12 Colour Pencils Pack',890.00,45,'Assets/Product Page/Maped Color Peps Star 12 Colour Pencils Pack.jpg'),
('P017','Pencil Holder Pen Holder',690.00,30,'Assets/Product Page/pencil-holder-pen-holder-3.jpg'),
('P018','Pentium Pastels 24 Colour',950.00,40,'Assets/Product Page/Pentium Pastels 24 Colour.jpg');

INSERT IGNORE INTO product_categories (product_code, category_id)
SELECT p.code, c.category_id FROM (
 SELECT 'P001' code,'books' cat UNION SELECT 'P001','school-items'
 UNION SELECT 'P002','books' UNION SELECT 'P002','school-items'
 UNION SELECT 'P003','books' UNION SELECT 'P003','school-items'
 UNION SELECT 'P004','books' UNION SELECT 'P004','office-items'
 UNION SELECT 'P005','school-items' UNION SELECT 'P005','office-items'
 UNION SELECT 'P006','writing-tools' UNION SELECT 'P006','colour-products'
 UNION SELECT 'P007','writing-tools' UNION SELECT 'P007','colour-products'
 UNION SELECT 'P008','school-items'
 UNION SELECT 'P009','school-items' UNION SELECT 'P009','writing-tools'
 UNION SELECT 'P010','school-items' UNION SELECT 'P010','writing-tools'
 UNION SELECT 'P011','writing-tools' UNION SELECT 'P011','school-items'
 UNION SELECT 'P012','colour-products' UNION SELECT 'P012','school-items'
 UNION SELECT 'P013','school-items' UNION SELECT 'P013','office-items'
 UNION SELECT 'P014','office-items' UNION SELECT 'P014','desk-accessories'
 UNION SELECT 'P015','office-items' UNION SELECT 'P015','desk-accessories'
 UNION SELECT 'P016','colour-products' UNION SELECT 'P016','school-items'
 UNION SELECT 'P017','desk-accessories' UNION SELECT 'P017','office-items'
 UNION SELECT 'P018','colour-products'
) p JOIN categories c ON c.category_name = p.cat;
