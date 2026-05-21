-- =====================================================
-- INSERT INVENTORY ITEMS FROM PDF
-- Follows InventoryController@store logic
-- =====================================================

-- Disable foreign key checks temporarily
SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================
-- 1. MEAT & POULTRY (Category ID: 9)
-- =====================================================

-- Beef sausage
SET @item_name = 'Beef sausage';
SET @category_id = 9;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 56000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Whole chicken
SET @item_name = 'Whole chicken';
SET @category_id = 9;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 14000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Chicken filet
SET @item_name = 'Chicken filet';
SET @category_id = 9;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 22000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Mixed meat
SET @item_name = 'Mixed meat';
SET @category_id = 9;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 18000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Chicken wings
SET @item_name = 'Chicken wings';
SET @category_id = 9;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 14000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Tilapia filet
SET @item_name = 'Tilapia filet';
SET @category_id = 9;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 39000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Minced meat
SET @item_name = 'Minced meat';
SET @category_id = 9;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 10000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Beef fillet
SET @item_name = 'Beef fillet';
SET @category_id = 9;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 28000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Fish fillet
SET @item_name = 'Fish fillet';
SET @category_id = 9;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 30000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Pork fillet
SET @item_name = 'Pork fillet';
SET @category_id = 9;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 21000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Pork ribs
SET @item_name = 'Pork ribs';
SET @category_id = 9;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 30000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Bacon
SET @item_name = 'Bacon';
SET @category_id = 9;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 35000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Goat ribs
SET @item_name = 'Goat ribs';
SET @category_id = 9;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 20000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Ham
SET @item_name = 'Ham';
SET @category_id = 9;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 3000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Salmon
SET @item_name = 'Salmon';
SET @category_id = 9;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 310000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Prawns
SET @item_name = 'Prawns';
SET @category_id = 9;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 99300;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Lobster tails
SET @item_name = 'Lobster tails';
SET @category_id = 9;
SET @metrics = 'piece';
SET @base_unit = 'piece';
SET @quantity = 50;
SET @unit_cost = 5762;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Whole fish
SET @item_name = 'Whole fish';
SET @category_id = 9;
SET @metrics = 'piece';
SET @base_unit = 'piece';
SET @quantity = 50;
SET @unit_cost = 15000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Whole pig
SET @item_name = 'Whole pig';
SET @category_id = 9;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 20000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Goat whole leg
SET @item_name = 'Goat whole leg';
SET @category_id = 9;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 27000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Whole local chicken
SET @item_name = 'Whole local chicken';
SET @category_id = 9;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 40000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Top side
SET @item_name = 'Top side';
SET @category_id = 9;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 20000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Cow pease
SET @item_name = 'Cow pease';
SET @category_id = 9;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 10000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Rib eye steak
SET @item_name = 'Rib eye steak';
SET @category_id = 9;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 23000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- =====================================================
-- 2. DAIRY (Category ID: 8)
-- =====================================================

-- Eggs
SET @item_name = 'Eggs';
SET @category_id = 8;
SET @metrics = 'piece';
SET @base_unit = 'piece';
SET @quantity = 500;
SET @unit_cost = 428;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Mozzarella cheese
SET @item_name = 'Mozzarella cheese';
SET @category_id = 8;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 22000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Cheddar cheese
SET @item_name = 'Cheddar cheese';
SET @category_id = 8;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 22000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Parmesan cheese
SET @item_name = 'Parmesan cheese';
SET @category_id = 8;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 50;
SET @unit_cost = 79000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Unsalted butter
SET @item_name = 'Unsalted butter';
SET @category_id = 8;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 50;
SET @unit_cost = 204000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Milk
SET @item_name = 'Milk';
SET @category_id = 8;
SET @metrics = 'litre';
SET @base_unit = 'litre';
SET @quantity = 200;
SET @unit_cost = 3000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Cooking cream
SET @item_name = 'Cooking cream';
SET @category_id = 8;
SET @metrics = 'litre';
SET @base_unit = 'litre';
SET @quantity = 100;
SET @unit_cost = 17500;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Croma butter
SET @item_name = 'Croma butter';
SET @category_id = 8;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 50;
SET @unit_cost = 105000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- =====================================================
-- 3. PRODUCE / VEGETABLES (Category ID: 10)
-- =====================================================

-- Chips/Potato wedges
SET @item_name = 'Chips/Potato wedges';
SET @category_id = 10;
SET @metrics = 'portion';
SET @base_unit = 'portion';
SET @quantity = 500;
SET @unit_cost = 1335;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Tomatoes
SET @item_name = 'Tomatoes';
SET @category_id = 10;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 200;
SET @unit_cost = 2700;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Lettuce
SET @item_name = 'Lettuce';
SET @category_id = 10;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 3500;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Onions
SET @item_name = 'Onions';
SET @category_id = 10;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 200;
SET @unit_cost = 3000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Pickles
SET @item_name = 'Pickles';
SET @category_id = 10;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 50;
SET @unit_cost = 15000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Spring onions
SET @item_name = 'Spring onions';
SET @category_id = 10;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 50;
SET @unit_cost = 2000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Yellow pepper
SET @item_name = 'Yellow pepper';
SET @category_id = 10;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 50;
SET @unit_cost = 1500;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Pineapple
SET @item_name = 'Pineapple';
SET @category_id = 10;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 1500;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Coriander
SET @item_name = 'Coriander';
SET @category_id = 10;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 50;
SET @unit_cost = 1000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Chinese cabbage
SET @item_name = 'Chinese cabbage';
SET @category_id = 10;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 50;
SET @unit_cost = 5000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Cabbage
SET @item_name = 'Cabbage';
SET @category_id = 10;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 2000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Avocado
SET @item_name = 'Avocado';
SET @category_id = 10;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 2000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Cucumber
SET @item_name = 'Cucumber';
SET @category_id = 10;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 3000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Broccoli
SET @item_name = 'Broccoli';
SET @category_id = 10;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 5000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Cauliflower
SET @item_name = 'Cauliflower';
SET @category_id = 10;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 2500;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- French beans
SET @item_name = 'French beans';
SET @category_id = 10;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 3000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Irish potatoes
SET @item_name = 'Irish potatoes';
SET @category_id = 10;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 200;
SET @unit_cost = 2500;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Baby potatoes
SET @item_name = 'Baby potatoes';
SET @category_id = 10;
SET @metrics = 'portion';
SET @base_unit = 'portion';
SET @quantity = 500;
SET @unit_cost = 2876;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Zucchini
SET @item_name = 'Zucchini';
SET @category_id = 10;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 4000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Leafy lettuce
SET @item_name = 'Leafy lettuce';
SET @category_id = 10;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 2500;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Bell peppers
SET @item_name = 'Bell peppers';
SET @category_id = 10;
SET @metrics = 'piece';
SET @base_unit = 'piece';
SET @quantity = 500;
SET @unit_cost = 11;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- =====================================================
-- 4. SAUCES & CONDIMENTS (Category ID: 13)
-- =====================================================

-- 1000 island sauce
SET @item_name = '1000 island sauce';
SET @category_id = 13;
SET @metrics = 'portion';
SET @base_unit = 'portion';
SET @quantity = 500;
SET @unit_cost = 831;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Dark soy sauce
SET @item_name = 'Dark soy sauce';
SET @category_id = 13;
SET @metrics = 'litre';
SET @base_unit = 'litre';
SET @quantity = 100;
SET @unit_cost = 8000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Bbq sauce
SET @item_name = 'Bbq sauce';
SET @category_id = 13;
SET @metrics = 'litre';
SET @base_unit = 'litre';
SET @quantity = 100;
SET @unit_cost = 17000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Concasse sauce
SET @item_name = 'Concasse sauce';
SET @category_id = 13;
SET @metrics = 'portion';
SET @base_unit = 'portion';
SET @quantity = 500;
SET @unit_cost = 1264;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Oyster sauce
SET @item_name = 'Oyster sauce';
SET @category_id = 13;
SET @metrics = 'litre';
SET @base_unit = 'litre';
SET @quantity = 100;
SET @unit_cost = 17000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Bechamel sauce
SET @item_name = 'Bechamel sauce';
SET @category_id = 13;
SET @metrics = 'portion';
SET @base_unit = 'portion';
SET @quantity = 500;
SET @unit_cost = 1000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Vinaigrette dressing
SET @item_name = 'Vinaigrette dressing';
SET @category_id = 13;
SET @metrics = 'portion';
SET @base_unit = 'portion';
SET @quantity = 500;
SET @unit_cost = 5435;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Sweet chilli sauce
SET @item_name = 'Sweet chilli sauce';
SET @category_id = 13;
SET @metrics = 'litre';
SET @base_unit = 'litre';
SET @quantity = 100;
SET @unit_cost = 17000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Mayonnaise
SET @item_name = 'Mayonnaise';
SET @category_id = 13;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 30000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Tomato sauce
SET @item_name = 'Tomato sauce';
SET @category_id = 13;
SET @metrics = 'litre';
SET @base_unit = 'litre';
SET @quantity = 100;
SET @unit_cost = 8000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Worcestershire sauce
SET @item_name = 'Worcestershire sauce';
SET @category_id = 13;
SET @metrics = 'litre';
SET @base_unit = 'litre';
SET @quantity = 100;
SET @unit_cost = 12000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Light soy sauce
SET @item_name = 'Light soy sauce';
SET @category_id = 13;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 8500;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- =====================================================
-- 5. SPICES & SEASONINGS (Category ID: 14)
-- =====================================================

-- Salt
SET @item_name = 'Salt';
SET @category_id = 14;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 500;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Brown sugar
SET @item_name = 'Brown sugar';
SET @category_id = 14;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 15000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- White pepper
SET @item_name = 'White pepper';
SET @category_id = 14;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 50;
SET @unit_cost = 30000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Black pepper
SET @item_name = 'Black pepper';
SET @category_id = 14;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 50;
SET @unit_cost = 35000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Paprika
SET @item_name = 'Paprika';
SET @category_id = 14;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 50;
SET @unit_cost = 25000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Rosemary
SET @item_name = 'Rosemary';
SET @category_id = 14;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 50;
SET @unit_cost = 2000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Garlic and ginger paste
SET @item_name = 'Garlic and ginger paste';
SET @category_id = 14;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 36343;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Meat tenderizer
SET @item_name = 'Meat tenderizer';
SET @category_id = 14;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 50;
SET @unit_cost = 15000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Cumin
SET @item_name = 'Cumin';
SET @category_id = 14;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 50;
SET @unit_cost = 35000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Oregano
SET @item_name = 'Oregano';
SET @category_id = 14;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 50;
SET @unit_cost = 25000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Thyme
SET @item_name = 'Thyme';
SET @category_id = 14;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 50;
SET @unit_cost = 1000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Honey
SET @item_name = 'Honey';
SET @category_id = 14;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 50;
SET @unit_cost = 18000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Cinnamon
SET @item_name = 'Cinnamon';
SET @category_id = 14;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 50;
SET @unit_cost = 25000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Nutmeg
SET @item_name = 'Nutmeg';
SET @category_id = 14;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 50;
SET @unit_cost = 25000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Chilli flex
SET @item_name = 'Chilli flex';
SET @category_id = 14;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 50;
SET @unit_cost = 25000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Tikka masala
SET @item_name = 'Tikka masala';
SET @category_id = 14;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 50;
SET @unit_cost = 33000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Pilau masala
SET @item_name = 'Pilau masala';
SET @category_id = 14;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 50;
SET @unit_cost = 15000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Dry thyme
SET @item_name = 'Dry thyme';
SET @category_id = 14;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 50;
SET @unit_cost = 25000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Sage
SET @item_name = 'Sage';
SET @category_id = 14;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 50;
SET @unit_cost = 30000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Curry powder
SET @item_name = 'Curry powder';
SET @category_id = 14;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 50;
SET @unit_cost = 25000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Turmeric
SET @item_name = 'Turmeric';
SET @category_id = 14;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 50;
SET @unit_cost = 28000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Cardamom
SET @item_name = 'Cardamom';
SET @category_id = 14;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 50;
SET @unit_cost = 30000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Ginger
SET @item_name = 'Ginger';
SET @category_id = 14;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 50;
SET @unit_cost = 25000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- =====================================================
-- 6. DRY GOODS (Category ID: 11)
-- =====================================================

-- Dough
SET @item_name = 'Dough';
SET @category_id = 11;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 1831;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Pasta
SET @item_name = 'Pasta';
SET @category_id = 11;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 200;
SET @unit_cost = 4500;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Rice
SET @item_name = 'Rice';
SET @category_id = 11;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 200;
SET @unit_cost = 9000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Steamed rice (portion)
SET @item_name = 'Steamed rice';
SET @category_id = 11;
SET @metrics = 'portion';
SET @base_unit = 'portion';
SET @quantity = 500;
SET @unit_cost = 2127;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Vegetable rice (portion)
SET @item_name = 'Vegetable rice';
SET @category_id = 11;
SET @metrics = 'portion';
SET @base_unit = 'portion';
SET @quantity = 500;
SET @unit_cost = 2460;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- =====================================================
-- 7. BAKERY (Category ID: 15)
-- =====================================================

-- Burger bun
SET @item_name = 'Burger bun';
SET @category_id = 15;
SET @metrics = 'piece';
SET @base_unit = 'piece';
SET @quantity = 500;
SET @unit_cost = 500;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Bread
SET @item_name = 'Bread';
SET @category_id = 15;
SET @metrics = 'piece';
SET @base_unit = 'piece';
SET @quantity = 500;
SET @unit_cost = 1000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Slider bars
SET @item_name = 'Slider bars';
SET @category_id = 15;
SET @metrics = 'piece';
SET @base_unit = 'piece';
SET @quantity = 500;
SET @unit_cost = 512;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Pizza box
SET @item_name = 'Pizza box';
SET @category_id = 15;
SET @metrics = 'piece';
SET @base_unit = 'piece';
SET @quantity = 500;
SET @unit_cost = 1000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Springroll sheet
SET @item_name = 'Springroll sheet';
SET @category_id = 15;
SET @metrics = 'piece';
SET @base_unit = 'piece';
SET @quantity = 500;
SET @unit_cost = 288;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- =====================================================
-- 8. OILS & FATS (Category ID: 12)
-- =====================================================

-- Cooking oil
SET @item_name = 'Cooking oil';
SET @category_id = 12;
SET @metrics = 'litre';
SET @base_unit = 'litre';
SET @quantity = 200;
SET @unit_cost = 7250;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- =====================================================
-- 9. BEVERAGES / SOFT DRINKS (Category ID: 1)
-- =====================================================

-- Soft drink
SET @item_name = 'Soft drink';
SET @category_id = 1;
SET @metrics = 'bottle';
SET @base_unit = 'bottle';
SET @quantity = 500;
SET @unit_cost = 813;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Castle Lite
SET @item_name = 'Castle Lite';
SET @category_id = 1;
SET @metrics = 'bottle';
SET @base_unit = 'bottle';
SET @quantity = 500;
SET @unit_cost = 2400;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- =====================================================
-- 10. WINE (Category ID: 6)
-- =====================================================

-- Four cousins dry white
SET @item_name = 'Four cousins dry white';
SET @category_id = 6;
SET @metrics = 'litre';
SET @base_unit = 'litre';
SET @quantity = 100;
SET @unit_cost = 55000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Fragolino
SET @item_name = 'Fragolino';
SET @category_id = 6;
SET @metrics = 'glass';
SET @base_unit = 'glass';
SET @quantity = 500;
SET @unit_cost = 9000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- =====================================================
-- 11. JUICES / BEVERAGES
-- =====================================================

-- Juice
SET @item_name = 'Juice';
SET @category_id = 1;
SET @metrics = 'glass';
SET @base_unit = 'glass';
SET @quantity = 500;
SET @unit_cost = 9015;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Nojitos
SET @item_name = 'Nojitos';
SET @category_id = 1;
SET @metrics = 'glass';
SET @base_unit = 'glass';
SET @quantity = 500;
SET @unit_cost = 854;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Lemon juice
SET @item_name = 'Lemon juice';
SET @category_id = 1;
SET @metrics = 'litre';
SET @base_unit = 'litre';
SET @quantity = 100;
SET @unit_cost = 3000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- =====================================================
-- 12. FRUITS (Category ID: 10)
-- =====================================================

-- Oranges
SET @item_name = 'Oranges';
SET @category_id = 10;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 10000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Watermelon
SET @item_name = 'Watermelon';
SET @category_id = 10;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 7000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Apples
SET @item_name = 'Apples';
SET @category_id = 10;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 50;
SET @unit_cost = 175000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- =====================================================
-- 13. MISC / OTHER
-- =====================================================

-- Pineapple natural tenderizer
SET @item_name = 'Pineapple natural tenderizer';
SET @category_id = 14;
SET @metrics = 'portion';
SET @base_unit = 'portion';
SET @quantity = 500;
SET @unit_cost = 30;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Balsamic vinegar
SET @item_name = 'Balsamic vinegar';
SET @category_id = 13;
SET @metrics = 'litre';
SET @base_unit = 'litre';
SET @quantity = 50;
SET @unit_cost = 13000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Chicken quarters
SET @item_name = 'Chicken quarters';
SET @category_id = 9;
SET @metrics = 'piece';
SET @base_unit = 'piece';
SET @quantity = 500;
SET @unit_cost = 16;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Chicken for pizza
SET @item_name = 'Chicken for pizza';
SET @category_id = 9;
SET @metrics = 'portion';
SET @base_unit = 'portion';
SET @quantity = 500;
SET @unit_cost = 3014;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Curry sauce
SET @item_name = 'Curry sauce';
SET @category_id = 13;
SET @metrics = 'portion';
SET @base_unit = 'portion';
SET @quantity = 500;
SET @unit_cost = 5000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Coconut milk
SET @item_name = 'Coconut milk';
SET @category_id = 13;
SET @metrics = 'litre';
SET @base_unit = 'litre';
SET @quantity = 100;
SET @unit_cost = 18000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Red food color
SET @item_name = 'Red food color';
SET @category_id = 14;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 10;
SET @unit_cost = 1000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- White vinegar
SET @item_name = 'White vinegar';
SET @category_id = 13;
SET @metrics = 'litre';
SET @base_unit = 'litre';
SET @quantity = 50;
SET @unit_cost = 26000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Vanilla essence
SET @item_name = 'Vanilla essence';
SET @category_id = 14;
SET @metrics = 'litre';
SET @base_unit = 'litre';
SET @quantity = 10;
SET @unit_cost = 5000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Sugar
SET @item_name = 'Sugar';
SET @category_id = 14;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 200;
SET @unit_cost = 210000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Prestige
SET @item_name = 'Prestige';
SET @category_id = 11;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 50;
SET @unit_cost = 105000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Yeast
SET @item_name = 'Yeast';
SET @category_id = 11;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 50;
SET @unit_cost = 16500;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Bread improver
SET @item_name = 'Bread improver';
SET @category_id = 11;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 20;
SET @unit_cost = 15000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Anchovies
SET @item_name = 'Anchovies';
SET @category_id = 9;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 20;
SET @unit_cost = 19000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Parsley
SET @item_name = 'Parsley';
SET @category_id = 10;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 10;
SET @unit_cost = 1000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Celery
SET @item_name = 'Celery';
SET @category_id = 10;
SET @metrics = 'bundle';
SET @base_unit = 'bundle';
SET @quantity = 100;
SET @unit_cost = 1000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Baking flour
SET @item_name = 'Baking flour';
SET @category_id = 11;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 100;
SET @unit_cost = 6600;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- =====================================================
-- 14. SOUPS / PORTIONS
-- =====================================================

-- Clear chicken vegetable soup
SET @item_name = 'Clear chicken vegetable soup';
SET @category_id = 13;
SET @metrics = 'portion';
SET @base_unit = 'portion';
SET @quantity = 500;
SET @unit_cost = 4801;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Beef Pilau
SET @item_name = 'Beef Pilau';
SET @category_id = 13;
SET @metrics = 'portion';
SET @base_unit = 'portion';
SET @quantity = 500;
SET @unit_cost = 12068;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Maze's mixed green salad
SET @item_name = 'Maze mixed green salad';
SET @category_id = 10;
SET @metrics = 'portion';
SET @base_unit = 'portion';
SET @quantity = 500;
SET @unit_cost = 1586;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Pork ribs portioned
SET @item_name = 'Pork ribs portioned';
SET @category_id = 9;
SET @metrics = 'portion';
SET @base_unit = 'portion';
SET @quantity = 500;
SET @unit_cost = 28902;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- =====================================================
-- 15. MATOKE / AFRICAN FOODS (Category ID: 10)
-- =====================================================

-- Matoke
SET @item_name = 'Matoke';
SET @category_id = 10;
SET @metrics = 'bunch';
SET @base_unit = 'bunch';
SET @quantity = 50;
SET @unit_cost = 40000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Matoke fingers
SET @item_name = 'Matoke fingers';
SET @category_id = 10;
SET @metrics = 'piece';
SET @base_unit = 'piece';
SET @quantity = 500;
SET @unit_cost = 300;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Plantain finger
SET @item_name = 'Plantain finger';
SET @category_id = 10;
SET @metrics = 'piece';
SET @base_unit = 'piece';
SET @quantity = 500;
SET @unit_cost = 700;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Yams
SET @item_name = 'Yams';
SET @category_id = 10;
SET @metrics = 'heap';
SET @base_unit = 'heap';
SET @quantity = 50;
SET @unit_cost = 5000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Cassava
SET @item_name = 'Cassava';
SET @category_id = 10;
SET @metrics = 'heap';
SET @base_unit = 'heap';
SET @quantity = 50;
SET @unit_cost = 5000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- =====================================================
-- 16. NUTS & PASTES (Category ID: 11)
-- =====================================================

-- G nut paste
SET @item_name = 'G nut paste';
SET @category_id = 11;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 50;
SET @unit_cost = 8000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Cashew nut
SET @item_name = 'Cashew nut';
SET @category_id = 11;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 50;
SET @unit_cost = 28000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Dates
SET @item_name = 'Dates';
SET @category_id = 11;
SET @metrics = 'kg';
SET @base_unit = 'kg';
SET @quantity = 50;
SET @unit_cost = 4000;
SET @item_code = CONCAT('ITEM-', UPPER(SUBSTRING(MD5(RAND()), 1, 13)));
SET @stock_before = 0;
SET @stock_after = @quantity;
SET @movement_number = CONCAT('STK-IN-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0'));

INSERT INTO inventory_items (item_code, name, category_id, default_unit_of_measure_id, base_unit, unit_cost, current_stock, is_active, created_by, created_at, updated_at)
VALUES (@item_code, @item_name, @category_id, @metrics, @base_unit, @unit_cost, @stock_after, 1, 1, NOW(), NOW());

SET @new_item_id = LAST_INSERT_ID();
INSERT INTO stock_movements (movement_number, inventory_item_id, store_id, movement_type_id, quantity, base_unit, quantity_in_base_unit, unit_cost, total_value, reason, movement_date, approved_at, approved_by, created_by, stock_before, stock_after)
VALUES (@movement_number, @new_item_id, 1, 2, @quantity, @base_unit, @quantity, @unit_cost, @quantity * @unit_cost, 'Manual inventory entry from PDF', NOW(), NOW(), 1, 1, @stock_before, @stock_after);

-- Enable foreign key checks again
SET FOREIGN_KEY_CHECKS = 1;
