#!/bin/bash

# GitHub Personal Access Token 设置脚本
echo "=== GitHub Personal Access Token 设置 ==="
echo
echo "请按以下步骤操作："
echo "1. 登录您的GitHub账户 (https://github.com/CoachSonia1314)"
echo "2. 进入 Settings > Developer settings > Personal access tokens"
echo "3. 点击 'Generate new token'"
echo "4. 选择以下权限："
echo "   - repo (完整仓库访问权限)"
echo "   - workflow (GitHub Actions)"
echo "5. 复制生成的token"
echo
echo "现在请输入您的GitHub Personal Access Token:"
read -s token

if [ -z "$token" ]; then
    echo "错误：Token不能为空"
    exit 1
fi

# 更新凭据文件
echo "https://CoachSonia1314:$token@github.com" > /var/www/www.nmnihealth.com/.git-credentials

# 设置文件权限
chmod 600 /var/www/www.nmnihealth.com/.git-credentials

echo "Token已设置成功！"
echo
echo "测试GitHub连接..."

# 测试连接
cd /var/www/www.nmnihealth.com
git push origin master

if [ $? -eq 0 ]; then
    echo "✅ GitHub连接测试成功！"
else
    echo "❌ GitHub连接失败，请检查Token是否正确"
fi