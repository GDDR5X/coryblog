# Coryzenの博客

一个基于PHP的轻量级个人博客系统，使用SQLite本地数据库存储数据，Markdown文件存储文章内容。

## 功能特点

- **轻量级架构**：使用SQLite本地数据库，无需额外配置
- **Markdown支持**：使用Parsedown库解析Markdown
- **响应式设计**：适配不同设备屏幕
- **完整的后台管理**：支持文章的创建、编辑、删除
- **评论系统**：支持访客评论，后台审核
- **分类系统**：文章可按分类组织
- **安全措施**：密码哈希存储，HTML转义防止XSS攻击，数据库文件受保护

## 目录结构

```
cory_blog/
├── admin/           # 管理后台
├── assets/          # 静态资源
├── data/            # 数据文件
│   ├── db/          # SQLite数据库文件
│   └── *.json       # JSON数据文件（保留用于兼容）
├── includes/        # 核心功能文件
├── posts/           # Markdown文章文件
├── index.php        # 首页
├── post.php         # 文章详情页
├── archive.php      # 归档页面
├── install.php      # 安装程序
└── run.bat          # 启动服务器脚本
```

## 安装说明

### 环境要求
- PHP 7.0 或更高版本
- Web服务器（如Apache、Nginx）或PHP内置服务器

### 快速开始
1. 克隆或下载项目到本地
2. 使用PHP内置服务器启动项目：
   ```bash
   php -S localhost:8000 router.php
   ```
   或使用提供的批处理脚本：
   ```bash
   run.bat
   ```
3. 访问 `http://localhost:8000/install.php` 运行安装程序
4. 创建管理员账户
5. 登录后台 `http://localhost:8000/admin/login.php` 开始管理博客

## 使用指南

### 发布文章
1. 登录后台管理界面
2. 点击"写文章"按钮
3. 填写文章标题、选择分类、编写内容
4. 选择"已发布"状态并保存

### 管理评论
1. 登录后台管理界面
2. 评论会存储在 SQLite 数据库中
3. 可在后台审核评论

### 自定义配置
- 修改 `includes/config.php` 文件中的 `SITE_NAME` 和 `SITE_URL`
- 修改 `assets/css/style.css` 自定义主题样式

## 数据存储

- **文章**：存储为Markdown文件，位于 `posts/` 目录
- **用户**：存储在 SQLite 数据库 `data/db/blog.db` 中
- **分类**：存储在 SQLite 数据库 `data/db/blog.db` 中
- **评论**：存储在 SQLite 数据库 `data/db/blog.db` 中

## 技术栈

- PHP 7.0+
- SQLite 本地数据库
- Markdown (Parsedown)
- HTML5 & CSS3
- Font Awesome

## 许可证

本项目采用 MIT 许可证 - 详见 [LICENSE](LICENSE) 文件

## 安全最佳实践

为了确保博客系统的安全，建议采取以下措施：

1. **定期更新密码**：管理员应定期更改密码，使用强密码（包含大小写字母、数字和特殊字符）
2. **限制登录尝试**：考虑添加登录尝试限制，防止暴力破解
3. **备份数据**：定期备份 `data/db/` 目录中的数据库文件
4. **禁用错误显示**：在生产环境中，确保 PHP 错误不会显示给用户
5. **使用 HTTPS**：在生产环境中启用 HTTPS，保护数据传输
6. **定期检查文件权限**：确保 `data/` 目录的权限设置正确（建议 755）
7. **更新依赖**：定期检查并更新 Parsedown 等依赖库
8. **限制管理后台访问**：考虑使用 IP 限制或其他方式限制管理后台的访问

## 迁移说明

如果您之前使用JSON存储，系统会自动兼容。建议在新安装中使用SQLite存储。

## 贡献

欢迎提交 Issue 和 Pull Request 来改进这个项目！
