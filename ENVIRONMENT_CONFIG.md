# 环境配置说明

## 本地开发环境

### 1. 使用本地PHP服务器
```bash
# 启动本地服务器
php -S localhost:8000
```

### 2. 使用ngrok进行外网访问
```bash
# 启动ngrok
ngrok http 8000
```

### 3. 访问方式
- 本地访问：http://localhost:8000
- 外网访问：https://xxx.ngrok.io (ngrok提供的URL)

## 生产环境配置

### 1. 修改 `includes/config.php` 中的生产环境URL

```php
// 生产环境配置 - 请根据实际情况修改
$production_url = 'https://yourdomain.com'; // 替换为您的实际域名
```

### 2. 部署到服务器
- 将整个项目上传到服务器
- 确保PHP版本 >= 7.4
- 确保服务器支持SMTP邮件发送

## 邮箱验证URL自动适应

系统会自动检测当前环境：

### 本地环境检测条件：
- `localhost` 域名
- `127.0.0.1` IP地址
- `.ngrok.io` 域名

### 生产环境检测条件：
- 其他所有域名（自动使用配置的生产URL）

## SMTP配置

当前使用QQ邮箱SMTP，配置如下：

```php
define('SMTP_HOST', 'smtp.qq.com');
define('SMTP_PORT', 465);
define('SMTP_USERNAME', 'your@qq.com');
define('SMTP_PASSWORD', 'your_auth_code');
define('SMTP_ENCRYPTION', 'ssl');
```

### 其他邮箱SMTP配置示例：

#### 163邮箱
```php
define('SMTP_HOST', 'smtp.163.com');
define('SMTP_PORT', 465);
define('SMTP_ENCRYPTION', 'ssl');
```

#### Gmail
```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_ENCRYPTION', 'tls');
```

#### 阿里云邮箱
```php
define('SMTP_HOST', 'smtp.aliyun.com');
define('SMTP_PORT', 465);
define('SMTP_ENCRYPTION', 'ssl');
```

## 测试建议

### 本地测试
1. 启动本地服务器：`php -S localhost:8000`
2. 访问个人中心：http://localhost:8000/profile.php
3. 发送验证邮件
4. 检查邮箱中的验证链接

### 生产环境测试
1. 确保配置了正确的生产URL
2. 访问生产环境网站
3. 测试邮箱验证功能
4. 验证链接是否指向正确的域名

## 注意事项

1. **安全提醒**：不要将SMTP密码或授权码提交到版本控制系统
2. **环境隔离**：建议为不同环境使用不同的配置文件
3. **URL配置**：确保生产环境URL使用HTTPS
4. **邮件发送**：某些服务器可能需要配置SMTP端口开放