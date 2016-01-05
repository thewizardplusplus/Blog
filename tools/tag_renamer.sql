-- for MySQL

SET @old_tag = 'MySQL';
SET @new_tag = '';

UPDATE `blog_posts`
SET `tags` = REPLACE(REPLACE(`tags`, @old_tag, @new_tag), ',,', ',');
