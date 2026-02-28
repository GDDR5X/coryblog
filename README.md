# Coryzenの博客

一个基于PHP的轻量级个人博客系统，使用Markdown文件存储文章内容，JSON文件存储其他数据。

## 功能特点

- **轻量级架构**：无数据库依赖，使用文件系统存储
- **Markdown支持**：使用Parsedown库解析Markdown
- **响应式设计**：适配不同设备屏幕
- **完整的后台管理**：支持文章的创建、编辑、删除
- **评论系统**：支持访客评论，后台审核
- **分类系统**：文章可按分类组织
- **安全措施**：密码哈希存储，HTML转义防止XSS攻击

## 目录结构

```
cory_blog/
├── admin/           # 管理后台
├── assets/          # 静态资源
├── data/            # JSON数据文件
├── includes/        # 核心功能文件
├── posts/           # Markdown文章文件
├── index.php        # 首页
├── post.php         # 文章详情页
├── archive.php      # 归档页面
├── install.php      # 安装程序
└── start_server.bat # 启动服务器脚本
```

## 安装说明

### 环境要求
- PHP 7.0 或更高版本
- Web服务器（如Apache、Nginx）或PHP内置服务器

### 快速开始
1. 克隆或下载项目到本地
2. 使用PHP内置服务器启动项目：
   ```bash
   php -S localhost:8000
   ```
   或使用提供的批处理脚本：
   ```bash
   start_server.bat
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
2. 评论会存储在 `data/comments.json` 文件中
3. 可直接编辑该文件审核评论（将 `is_approved` 设置为 1）

### 自定义配置
- 修改 `includes/config.php` 文件中的 `SITE_NAME` 和 `SITE_URL`
- 修改 `assets/css/style.css` 自定义主题样式

## 数据存储

- **文章**：存储为Markdown文件，位于 `posts/` 目录
- **用户**：存储在 `data/users.json` 文件中
- **分类**：存储在 `data/categories.json` 文件中
- **评论**：存储在 `data/comments.json` 文件中

## 技术栈

- PHP 7.0+
- Markdown (Parsedown)
- JSON
- HTML5 & CSS3
- Font Awesome

## 许可证

本项目采用 MIT 许可证 - 详见 [LICENSE](LICENSE) 文件

## 贡献

欢迎提交 Issue 和 Pull Request 来改进这个项目！
