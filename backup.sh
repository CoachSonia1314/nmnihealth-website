#!/bin/bash

# NmNiHealth网站自动备份脚本
# 作者: 系统管理员
# 日期: $(date +%Y-%m-%d)

# 设置变量
BACKUP_DIR="/var/backups/nmnihealth"
WEBSITE_DIR="/var/www/www.nmnihealth.com"
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_NAME="nmnihealth_backup_$DATE"

# 创建备份目录
mkdir -p $BACKUP_DIR

echo "开始备份 NmNiHealth 网站..."
echo "备份时间: $(date)"
echo "备份目录: $BACKUP_DIR"

# 1. Git提交当前更改
echo "正在提交Git更改..."
cd $WEBSITE_DIR
git add .
git commit -m "自动备份 - $DATE"

# 2. 推送到GitHub
echo "正在推送到GitHub..."
git push origin master

# 2. 创建数据库备份
echo "正在备份数据库..."
mysqldump -u nmnihealth -phealth2025 nmnihealth > $BACKUP_DIR/${BACKUP_NAME}_database.sql

# 3. 创建完整网站文件备份
echo "正在备份网站文件..."
tar -czf $BACKUP_DIR/${BACKUP_NAME}_files.tar.gz -C $WEBSITE_DIR .

# 4. 创建Git仓库备份
echo "正在备份Git仓库..."
tar -czf $BACKUP_DIR/${BACKUP_NAME}_git.tar.gz -C $WEBSITE_DIR .git

# 5. 清理旧备份（保留最近7天）
echo "正在清理旧备份..."
find $BACKUP_DIR -name "nmnihealth_backup_*" -type f -mtime +7 -delete

# 6. 创建备份报告
echo "正在生成备份报告..."
cat > $BACKUP_DIR/${BACKUP_NAME}_report.txt << EOF
NmNiHealth网站备份报告
=====================
备份时间: $(date)
备份类型: 完整备份

备份文件:
- 数据库备份: ${BACKUP_NAME}_database.sql
- 网站文件备份: ${BACKUP_NAME}_files.tar.gz
- Git仓库备份: ${BACKUP_NAME}_git.tar.gz

文件大小:
$(ls -lh $BACKUP_DIR/${BACKUP_NAME}_*.sql $BACKUP_DIR/${BACKUP_NAME}_*.tar.gz)

Git状态:
$(git -C $WEBSITE_DIR log --oneline -n 5)

磁盘空间:
$(df -h $BACKUP_DIR)

备份完成！
EOF

echo "备份完成！"
echo "备份文件保存在: $BACKUP_DIR"
echo "备份报告: $BACKUP_DIR/${BACKUP_NAME}_report.txt"

# 显示备份文件信息
echo -e "\n备份文件列表:"
ls -lh $BACKUP_DIR/${BACKUP_NAME}_*