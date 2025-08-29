-- Manual Reset Script for Chat Data
-- Run this in your MySQL client if Laravel commands fail

-- Disable foreign key checks
SET FOREIGN_KEY_CHECKS=0;

-- Clear all chat data
TRUNCATE TABLE chat_messages;
TRUNCATE TABLE chats;

-- Try to clear pusher tables (might not exist)
-- TRUNCATE TABLE pusher_messages;
-- TRUNCATE TABLE pusher_chats;

-- Reset auto increment
ALTER TABLE chats AUTO_INCREMENT = 1;
ALTER TABLE chat_messages AUTO_INCREMENT = 1;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS=1;

-- Check if data is cleared
SELECT COUNT(*) as chat_count FROM chats;
SELECT COUNT(*) as message_count FROM chat_messages;

-- Create sample data
INSERT INTO users (name, email, password, role, created_at, updated_at) 
VALUES ('احمد محمد', 'ahmed@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', NOW(), NOW())
ON DUPLICATE KEY UPDATE name = VALUES(name);

SET @customer_id = (SELECT id FROM users WHERE email = 'ahmed@test.com' LIMIT 1);

INSERT INTO chats (customer_id, subject, status, priority, admin_unread_count, customer_unread_count, last_message_at, created_at, updated_at)
VALUES 
(@customer_id, 'استفسار عن الطلب #12345', 'open', 'medium', 1, 0, NOW(), NOW(), NOW()),
(@customer_id, 'مشكلة في المنتج', 'open', 'high', 2, 0, NOW() - INTERVAL 30 MINUTE, NOW(), NOW());

SET @chat1_id = LAST_INSERT_ID();
SET @chat2_id = @chat1_id + 1;

INSERT INTO chat_messages (chat_id, sender_id, sender_type, message, message_type, metadata, created_at, updated_at)
VALUES 
(@chat1_id, @customer_id, 'customer', 'السلام عليكم، أريد الاستفسار عن حالة الطلب رقم #12345', 'text', '{"created_by":"manual_sql"}', NOW(), NOW()),
(@chat2_id, @customer_id, 'customer', 'المنتج وصل معطوب، أريد إرجاعه', 'text', '{"created_by":"manual_sql"}', NOW() - INTERVAL 30 MINUTE, NOW());

-- Verify sample data
SELECT c.id, c.subject, c.status, u.name as customer_name, COUNT(cm.id) as message_count
FROM chats c 
LEFT JOIN users u ON c.customer_id = u.id
LEFT JOIN chat_messages cm ON c.id = cm.chat_id
GROUP BY c.id, c.subject, c.status, u.name;

SELECT 'Sample data created successfully!' as result;
