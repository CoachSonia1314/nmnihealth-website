#!/bin/bash

# 简单的GitHub推送脚本
echo "=== 推送到GitHub ==="

cd /var/www/www.nmnihealth.com

# 添加所有更改
git add .

# 提交更改
git commit -m "自动提交 - $(date +'%Y-%m-%d %H:%M:%S')"

# 推送到GitHub
echo "正在推送到GitHub..."
git push origin master

echo "推送完成！"