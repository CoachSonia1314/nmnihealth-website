#!/bin/bash

# NmNiHealth网站手动备份脚本
# 包含GitHub推送功能

echo "=== NmNiHealth网站手动备份 ==="
echo "开始时间: $(date)"

# 设置变量
WEBSITE_DIR="/var/www/www.nmnihealth.com"
DATE=$(date +%Y%m%d_%H%M%S)

# 切换到网站目录
cd $WEBSITE_DIR

# 1. 检查Git状态
echo "检查Git状态..."
git status

# 2. 添加所有更改
echo "添加文件到Git..."
git add .

# 3. 提交更改
echo "提交更改..."
git commit -m "手动备份 - $DATE"

# 4. 推送到GitHub
echo "推送到GitHub..."
git push origin master

# 5. 显示最新提交
echo -e "\n最新提交记录:"
git log --oneline -n 5

echo -e "\n备份完成！"
echo "结束时间: $(date)"