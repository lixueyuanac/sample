# Laravel 论坛系统

## 系统架构
采用Laravel官方推荐的 Vagrant 和 HomeStead作为开发环境
采用Laravel 自带的权限认证系统以及用户系统
采用Laravel 自带的找回密码功能

## 系统部署

git clone git@github.com:lixueyuanac/sample.git

composer update

cp .env.example .env  修改数据库配置和redis配置（如没有可默认）、修改邮件发送配置

运行以下数据迁移脚本：
php artisan migrate
php artisan migrate:refresh seed

## 主要功能

系统为一个简单的微博系统，demo初步完成，其余功能后续完善中，另外bbs，cms系统开发中，请期待。。。


