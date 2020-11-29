create database wordpress default charset utf8mb4 default collate utf8mb4_collate_ci;
grant all privileges on wordpress.* to 'wordpress'@'%' identified by 'password';