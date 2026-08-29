# 尔意堂官网完整初始化恢复手册

本文用于服务器损坏、误删除、迁移或灾难恢复。目标是从空服务器恢复交付时的公开网站，而不是覆盖仍在运行的生产站。

## 1. 恢复结果

恢复完成后应具备：

- WordPress 7.1、PHP 8.3、MySQL 8.0；
- `eryitang` 主题和 `eryitang-core 0.4.4` 插件；
- 正式页面、文章、5个一级分类及其二级分类、医师、资质和全部媒体；
- 正式页面固定内容、广告、链接、Banner、备案、SEO/GEO 和结构化数据配置；
- `https://www.eytzyg.cn`、根域名及历史域名的跳转规则；
- 新创建的管理员账号，不恢复旧账号和旧会话。

## 2. 前提条件

- Linux服务器，建议与原环境一致；
- Nginx、PHP 8.3及常用WordPress扩展；
- MySQL 8.0；
- WP-CLI；
- 已解析的域名与有效TLS证书；
- 一个全新的空数据库和具备该库权限的数据库用户；
- 一个空的网站根目录。

恢复脚本仅对WP-CLI进程使用默认512 MB内存上限，避免小内存服务器在解压WordPress核心时失败；可通过`WP_CLI_MEMORY_LIMIT`调整，不会改动PHP-FPM的生产配置。

不要把恢复脚本直接指向仍在运行的生产目录或含有其他数据的数据库。

## 3. 校验仓库

在仓库根目录执行：

```bash
sha256sum -c MANIFEST.sha256
```

所有条目必须显示 `OK`。任何失败都应停止恢复并重新获取仓库。

## 4. 准备恢复变量

以下值只存在于当前终端，不要写回Git：

```bash
export SITE_ROOT=/www/wwwroot/demo.eytzyg.cn
export SITE_URL=https://www.eytzyg.cn
export DB_NAME=eryitang_demo
export DB_USER=replace_with_database_user
export DB_PASSWORD='replace_with_strong_database_password'
export DB_HOST=127.0.0.1
export ADMIN_USER=replace_with_new_admin
export ADMIN_EMAIL=replace_with_admin_email
export ADMIN_PASSWORD='replace_with_strong_admin_password'
```

如果是演练，必须使用独立目录、独立数据库和测试域名。

## 5. 执行初始化

```bash
sudo -E bash scripts/restore.sh
```

脚本会：

1. 检查必要变量和命令；
2. 拒绝非空站点目录和非空数据库；
3. 下载并校验WordPress 7.1；
4. 复制正式 `wp-content`；
5. 创建不含明文秘密的本机 `wp-config.php`；
6. 导入用户表结构和正式内容数据库；
7. 创建新的管理员；
8. 激活正式主题和插件；
9. 更新站点地址、管理员邮箱并刷新固定链接；
10. 设置基本权限并执行WordPress校验。

## 6. 配置 Nginx 和 HTTPS

`server/nginx-production.example.conf` 是交付时生产配置的参考副本。启用前至少核对：

- `root` 是否为实际 `SITE_ROOT`；
- PHP-FPM socket 是否与服务器一致；
- TLS证书和私钥路径是否存在；
- 日志目录是否存在；
- 域名是否已解析到当前服务器。

证书私钥不在GitHub中，必须从证书签发平台重新部署。配置完成后执行：

```bash
sudo nginx -t
sudo nginx -s reload
```

## 7. 临时域名恢复

若演练域名不是正式域名，导入后执行：

```bash
wp search-replace 'https://www.eytzyg.cn' "$SITE_URL" \
  --path="$SITE_ROOT" --all-tables --precise --skip-columns=guid --allow-root
wp option update home "$SITE_URL" --path="$SITE_ROOT" --allow-root
wp option update siteurl "$SITE_URL" --path="$SITE_ROOT" --allow-root
wp rewrite flush --hard --path="$SITE_ROOT" --allow-root
```

必须使用WP-CLI进行序列化安全替换，不要用普通文本替换SQL。

## 8. 恢复后验证

```bash
sudo -E bash scripts/verify-restored-site.sh
```

随后人工检查：

- 首页、品牌介绍、联系我们、文章列表、文章详情；
- 一个一级分类和一个二级分类；
- 1440px桌面端与390px手机端；
- 图片、菜单、页脚备案、电话、地图和广告链接；
- `/wp-sitemap.xml`、`/llms.txt`、canonical、description、JSON-LD；
- 后台文章、页面固定内容、页面图片、广告、链接、医师和资质菜单；
- HTTP到HTTPS及根域名跳转。

## 9. 与原站的已知差异

恢复后的公开页面、内容和媒体应与交付基线一致。唯一有意差异是：

- 原生产管理员账号不恢复；
- 数据库密码、WordPress salts和管理员密码重新生成；
- TLS证书需要在新服务器重新部署；
- 缓存、计划任务执行时间和系统日志会重新生成。

仓库中的中文媒体文件名使用正确UTF-8名称，避免Windows归档工具造成乱码；媒体内容和WordPress附件路径与正式数据库一致。

这些差异不影响公开网站内容与功能。

为保持文章作者关系完整，新管理员会以用户ID 1创建；恢复脚本会校验这一点。

## 10. 回切原则

恢复演练通过前不要修改DNS。正式切换前冻结旧站内容，重新导出最后增量数据，缩短DNS TTL，确认新站HTTPS、邮件、后台和全部页面后再切换。旧服务器至少保留一个观察周期，不要在切换当天删除。
