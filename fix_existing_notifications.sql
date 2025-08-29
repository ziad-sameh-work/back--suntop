-- Fix existing notifications that don't have the new fields
-- Run this SQL script in your database management tool (phpMyAdmin, etc.)

-- First, check if the new columns exist
-- If not, run the migration first: php artisan migrate

-- Update existing notifications to have default values for new fields
UPDATE notifications 
SET 
    alert_type = 'info',
    target_type = 'user',
    body = NULL
WHERE 
    alert_type IS NULL 
    OR target_type IS NULL;

-- Optional: Add some sample data for testing
-- INSERT INTO notifications (title, message, body, type, alert_type, user_id, target_type, priority, created_at, updated_at) 
-- VALUES 
-- ('إشعار تجريبي', 'هذا إشعار للتجربة', 'تفاصيل إضافية للاختبار', 'general', 'info', 1, 'user', 'medium', NOW(), NOW());
