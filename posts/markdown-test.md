---
title: 测试文章
created_at: 2026-03-04 13:53:49
category_id: 1
status: published
---

# Markdown测试文档

这是一个用于测试Markdown、Mermaid图表和LaTeX数学公式的示例文章。

## 1. 基础Markdown语法

### 标题
# 一级标题
## 二级标题
### 三级标题

### 文本格式
- **粗体文本**
- *斜体文本*
- ***粗斜体文本***
- ~~删除线文本~~

### 列表
#### 无序列表
- 项目一
- 项目二
  - 子项目1
  - 子项目2
- 项目三

#### 有序列表
1. 第一步
2. 第二步
3. 第三步

### 引用
> 这是一个引用块
> 可以包含多行内容

### 链接和图片
- [Google](https://www.google.com)
- [GitHub](https://github.com)

### 表格
| 列1 | 列2 | 列3 |
|-----|-----|-----|
| 数据1 | 数据2 | 数据3 |
| 数据4 | 数据5 | 数据6 |

### 代码块
```python
def hello_world():
    print("Hello, World!")
    return True
```

```javascript
const greeting = "Hello, World!";
console.log(greeting);
```

```powershell
# PowerShell 示例
Get-Process | Where-Object { $_.CPU -gt 100 }

function Get-SystemInfo {
    param(
        [string]$ComputerName = $env:COMPUTERNAME
    )
    
    Get-WmiObject -Class Win32_ComputerSystem -ComputerName $ComputerName
}

# 调用函数
Get-SystemInfo
```

## 2. Mermaid图表测试

### 流程图
```mermaid
graph TD
    A[开始] --> B{判断条件}
    B -->|是| C[执行操作1]
    B -->|否| D[执行操作2]
    C --> E[结束]
    D --> E
```

### 序列图
```mermaid
sequenceDiagram
    participant Client
    participant Server
    participant Database
    
    Client->>Server: 请求数据
    Server->>Database: 查询数据
    Database-->>Server: 返回数据
    Server-->>Client: 响应请求
```

### 类图
```mermaid
classDiagram
    class Animal {
        +String name
        +int age
        +makeSound()
    }
    
    class Dog {
        +bark()
    }
    
    class Cat {
        +meow()
    }
    
    Animal <|-- Dog
    Animal <|-- Cat
```

### 状态图
```mermaid
stateDiagram-v2
    [*] --> 进行中
    进行中 --> 完成
    完成 --> [*]
    进行中 --> 暂停
    暂停 --> 进行中
```

### 甘特图
```mermaid
gantt
    title 项目进度计划
    dateFormat  YYYY-MM-DD
    section 开发阶段
    需求分析       :a1, 2026-03-01, 5d
    设计阶段       :a2, after a1, 7d
    编码阶段       :a3, after a2, 10d
    section 测试阶段
    单元测试       :2026-03-20, 5d
    集成测试       :2026-03-25, 3d
```

## 3. LaTeX数学公式测试

### 行内公式
这是一个行内公式示例：$E = mc^2$，这是爱因斯坦的质能方程。

另一个例子：$a^2 + b^2 = c^2$ 是勾股定理。

### 块级公式

#### 二次方程求根公式
$$x = \frac{-b \pm \sqrt{b^2 - 4ac}}{2a}$$

#### 定积分
$$\int_{0}^{\infty} e^{-x^2} dx = \frac{\sqrt{\pi}}{2}$$

#### 傅里叶变换
$$\mathcal{F}\{f(x)\}(\xi) = \int_{-\infty}^{\infty} f(x) e^{-2\pi i x \xi} dx$$

#### 矩阵运算
$$
\begin{bmatrix}
1 & 2 & 3 \\
4 & 5 & 6 \\
7 & 8 & 9
\end{bmatrix}
\times
\begin{bmatrix}
a \\
b \\
c
\end{bmatrix}
=
\begin{bmatrix}
a + 2b + 3c \\
4a + 5b + 6c \\
7a + 8b + 9c
\end{bmatrix}
$$

#### 求和公式
$$\sum_{n=1}^{\infty} \frac{1}{n^2} = \frac{\pi^2}{6}$$

#### 欧拉公式
$$e^{ix} = \cos x + i \sin x$$

#### 偏微分方程
$$\frac{\partial u}{\partial t} = \alpha \frac{\partial^2 u}{\partial x^2}$$

### 复杂公式

#### 麦克斯韦方程组
$$
\begin{aligned}
\nabla \cdot \mathbf{E} &= \frac{\rho}{\varepsilon_0} \\
\nabla \cdot \mathbf{B} &= 0 \\
\nabla \times \mathbf{E} &= -\frac{\partial \mathbf{B}}{\partial t} \\
\nabla \times \mathbf{B} &= \mu_0 \mathbf{J} + \mu_0 \varepsilon_0 \frac{\partial \mathbf{E}}{\partial t}
\end{aligned}
$$

#### 波尔兹曼分布
$$P(i) = \frac{e^{-E_i / k_B T}}{\sum_j e^{-E_j / k_B T}}$$

## 4. 混合使用

这是一个包含代码、公式和图表的综合示例：

### 代码中的数学公式
```python
# 计算圆的面积
import math

radius = 5
area = math.pi * radius ** 2  # A = πr²
print(f"圆的面积是: {area:.2f}")
```

### 流程图 + 公式
```mermaid
graph LR
    A[输入半径 r] --> B[计算面积 A = πr²]
    B --> C[输出结果]
```

$$A = \pi r^2$$

## 5. 表格中的公式

| 公式名称 | 公式 | 说明 |
|---------|------|------|
| 勾股定理 | $a^2 + b^2 = c^2$ | 直角三角形三边关系 |
| 质能方程 | $E = mc^2$ | 质量与能量转换 |
| 牛顿第二定律 | $F = ma$ | 力与加速度关系 |

```html
<div class="interactive-widget">
    <h3>交互小部件</h3>
    <button onclick="alert('点击成功！')">点击我</button>
</div>
```

```css
.interactive-widget {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
}
.interactive-widget button {
    background: white;
    color: #667eea;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}
.interactive-widget button:hover {
    transform: scale(1.05);
}
```

```javascript
console.log('交互小部件已加载');
```
