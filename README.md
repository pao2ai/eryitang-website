# 尔意堂品牌官网｜正式恢复备份

本仓库 `main` 分支是 `https://www.eytzyg.cn` 在 2026-08-29 正式交付时的服务器外恢复基线。它不是设计草稿仓库，也不是过程文件归档。

目标是在一台干净的 Linux 服务器上，使用本仓库恢复出与交付时一致的公开网站：相同页面、文章、分类、图片、主题、插件、后台业务配置、SEO/GEO 输出及 Nginx 路由行为。

## 交付基线

| 项目 | 正式基线 |
| --- | --- |
| 主域名 | `https://www.eytzyg.cn` |
| 快照日期 | 2026-08-29（Asia/Shanghai） |
| WordPress | 7.1 |
| PHP | 8.3 |
| MySQL | 8.0 |
| Nginx | 1.30.4 |
| 主题 | `eryitang` 0.1.0 |
| 功能插件 | `eryitang-core` 0.4.4 |
| 数据库表前缀 | `wp_` |
| 固定链接 | `/%postname%/` |

## 仓库结构

```text
database/                 正式内容与配置数据库；不含原管理员数据
docs/                     恢复手册、实现复盘与长期知识库
scripts/                  初始化恢复和恢复后检查脚本
server/                   正式 Nginx 配置参考
site/wp-content/          生产主题、插件、媒体库和中文语言包
MANIFEST.sha256           交付文件 SHA-256 清单
```

## 一致性与安全边界

- `site/wp-content/` 由生产服务器导出，不以本地 Studio 副本冒充正式版本。导出时统一修复了30个中文媒体文件名和7个旧主题素材文件名的编码；文件内容、正式数据库引用和当前公开页面保持生产值。
- `database/production-content.sql` 保存正式页面、文章、分类、媒体记录、主题/插件配置和 SEO/GEO 配置。
- 原生产管理员账号、密码哈希、会话令牌和用户资料没有进入仓库；恢复时必须创建新管理员。
- 数据库中的正式管理员邮箱已替换为 `restore-admin@example.com`，恢复脚本会改为恢复时提供的新邮箱。
- 不包含数据库密码、SSH 私钥、TLS 私钥、宝塔登录信息或其他服务器秘密。
- WordPress 核心不重复入库；恢复脚本固定下载 WordPress 7.1，并执行官方校验。
- 若恢复到原域名，可得到与交付时一致的公开站点；若使用临时域名，需执行安全的序列化搜索替换。

## 恢复入口

完整步骤见 [docs/RESTORE.md](docs/RESTORE.md)。推荐从仓库根目录执行：

```bash
sudo -E bash scripts/restore.sh
sudo -E bash scripts/verify-restored-site.sh
```

脚本要求通过环境变量提供数据库和新管理员信息，不接受仓库中的明文秘密。脚本默认只允许写入空数据库和空站点目录，避免误覆盖现有网站。

## 日常维护原则

1. 正式代码只维护 `site/wp-content/themes/eryitang/` 和 `site/wp-content/plugins/eryitang-core/`。
2. 文章、医师、资质、页面图片、固定内容、广告和链接从 WordPress 后台维护。
3. 修改前先建立分支；验证 PHP 8.3、桌面端和390px手机端后再合并。
4. 生产服务器不作为唯一源码位置；生产变更完成后必须回写仓库并更新恢复基线。
5. 数据库迁移、核心升级、批量媒体替换等高风险操作必须建立服务器外备份并演练恢复。

项目实现方法、问题复盘和同类项目 SOP 见 [docs/IMPLEMENTATION-RETROSPECTIVE.md](docs/IMPLEMENTATION-RETROSPECTIVE.md)。
