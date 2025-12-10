<?php
/**
 * 应用入口文件
 * 检查PHP版本、必要扩展和安装状态
 */

class AppBootstrap
{
    private const MIN_PHP_VERSION = '7.4';
    private const REQUIRED_EXTENSIONS = ['sg16'];
    
    public function __construct()
    {
        $this->checkEnvironment();
    }
    
    private function checkEnvironment(): void
    {
        // 检查PHP版本
        if (!$this->checkPhpVersion()) {
            $this->showError(
                "PHP版本不满足要求",
                "当前PHP版本: " . PHP_VERSION . 
                "<br>需要版本: PHP " . self::MIN_PHP_VERSION . " 或更高"
            );
        }
        
        // 检查必要扩展
        $missingExtensions = $this->checkExtensions();
        if (!empty($missingExtensions)) {
            $this->showError(
                "缺少必要的PHP扩展",
                "缺失扩展: " . implode(", ", $missingExtensions)
            );
        }
        
        // 检查安装状态并跳转
        $this->redirect();
    }
    
    private function checkPhpVersion(): bool
    {
        return version_compare(PHP_VERSION, self::MIN_PHP_VERSION, '>=');
    }
    
    private function checkExtensions(): array
    {
        $missing = [];
        foreach (self::REQUIRED_EXTENSIONS as $ext) {
            if (!extension_loaded($ext)) {
                $missing[] = $ext;
            }
        }
        return $missing;
    }
    
    private function redirect(): void
    {
        if (file_exists('config/db.php')) {
            header("Location: admin/index.php");
        } else {
            header("Location: install.php");
        }
        exit;
    }
    
    private function showError(string $title, string $message): void
    {
        ?>
        <!DOCTYPE html>
        <html lang="zh-CN">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo htmlspecialchars($title); ?></title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                body {
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 20px;
                }
                .error-container {
                    background: white;
                    border-radius: 10px;
                    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
                    max-width: 500px;
                    padding: 40px;
                    text-align: center;
                }
                .error-icon {
                    font-size: 48px;
                    margin-bottom: 20px;
                }
                .error-title {
                    font-size: 24px;
                    color: #333;
                    margin-bottom: 15px;
                    font-weight: 600;
                }
                .error-message {
                    font-size: 14px;
                    color: #666;
                    line-height: 1.6;
                    margin-bottom: 30px;
                    background: #f5f5f5;
                    padding: 15px;
                    border-radius: 5px;
                }
                .error-info {
                    text-align: left;
                    font-family: 'Courier New', monospace;
                    font-size: 13px;
                    color: #555;
                }
            </style>
        </head>
        <body>
            <div class="error-container">
                <div class="error-icon">⚠️</div>
                <h1 class="error-title"><?php echo htmlspecialchars($title); ?></h1>
                <div class="error-message error-info">
                    <?php echo $message; ?>
                </div>
                <p style="color: #999; font-size: 12px;">请联系管理员或查看服务器配置</p>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

// 启动应用
new AppBootstrap();
