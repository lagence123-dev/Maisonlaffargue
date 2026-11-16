<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class Datalog123Inventory
{
    const MAX_FILES = 25000;
    const MAX_CHANGES = 500;

    public static function collect()
    {
        $theme = (string) Configuration::get('PS_THEME_NAME');
        $themeDir = _PS_ALL_THEMES_DIR_ . $theme;
        $themeConfig = array();
        foreach (array('config/theme.yml', 'config/theme.yaml') as $filename) {
            $path = $themeDir . '/' . $filename;
            if (is_readable($path) && filesize($path) < 1024 * 1024) {
                // Do not parse arbitrary YAML or expose full config: only safe metadata.
                $content = file_get_contents($path);
                foreach (array('name', 'display_name', 'version', 'author', 'parent') as $key) {
                    if (preg_match('/^' . preg_quote($key, '/') . ':\s*[\'\"]?([^\r\n\'\"]+)/m', $content, $match)) {
                        $themeConfig[$key] = trim($match[1]);
                    }
                }
                break;
            }
        }

        $modules = array();
        $installed = Db::getInstance()->executeS('SELECT name, version, active FROM `' . _DB_PREFIX_ . 'module` ORDER BY name');
        foreach ((array) $installed as $row) {
            $modules[] = array('name' => $row['name'], 'version' => $row['version'], 'active' => (bool) $row['active'], 'update_available' => null);
        }
        $overrides = array();
        $overrideBase = _PS_ROOT_DIR_ . '/override';
        if (is_dir($overrideBase)) {
            foreach (self::walkFiles($overrideBase, 'override') as $file => $abs) {
                $overrides[] = $file;
            }
        }
        sort($overrides);

        return array(
            'schema_version' => '1.0',
            'generated_at' => gmdate('c'),
            'shop' => array('domain' => (string) Configuration::get('PS_SHOP_DOMAIN'), 'prestashop_version' => _PS_VERSION_),
            'theme' => array('directory' => $theme, 'metadata' => $themeConfig),
            'mail' => array(
                'transport' => (int) Configuration::get('PS_MAIL_METHOD'),
                'smtp_server' => (string) Configuration::get('PS_MAIL_SERVER'),
                'smtp_port' => (int) Configuration::get('PS_MAIL_SMTP_PORT'),
                'smtp_encryption' => (string) Configuration::get('PS_MAIL_SMTP_ENCRYPTION'),
                'smtp_user_configured' => (bool) Configuration::get('PS_MAIL_USER'),
                'smtp_password_configured' => (bool) Configuration::get('PS_MAIL_PASSWD'),
                'shop_email' => (string) Configuration::get('PS_SHOP_EMAIL'),
            ),
            'modules' => $modules,
            'module_updates' => array('status' => 'unknown', 'reason' => 'Requires trusted version feed or back-office update metadata; never inferred from installed versions.'),
            'overrides' => $overrides,
            'files' => self::compareBaseline(),
        );
    }

    private static function roots()
    {
        $roots = array();
        foreach (array('themes', 'modules', 'override') as $dir) {
            if (is_dir(_PS_ROOT_DIR_ . '/' . $dir)) {
                $roots[$dir] = _PS_ROOT_DIR_ . '/' . $dir;
            }
        }
        return $roots;
    }

    private static function walkFiles($root, $prefix)
    {
        $result = array();
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->isLink()) {
                continue;
            }
            $abs = $file->getPathname();
            $relative = str_replace('\\', '/', substr($abs, strlen($root) + 1));
            if (preg_match('~(^|/)(vendor|node_modules|cache|var|\.git|uploads|mails|translations)/~i', '/' . $relative)) {
                continue;
            }
            if (!preg_match('/\.(php|tpl|twig|js|css|scss|json|ya?ml|xml|html|htaccess)$/i', $relative)) {
                continue;
            }
            $result[$prefix . '/' . $relative] = $abs;
        }
        return $result;
    }

    private static function currentFiles()
    {
        $files = array();
        foreach (self::roots() as $prefix => $root) {
            foreach (self::walkFiles($root, $prefix) as $key => $path) {
                $files[$key] = $path;
                if (count($files) > self::MAX_FILES) {
                    throw new RuntimeException('Too many source files to scan (limit: ' . self::MAX_FILES . ').');
                }
            }
        }
        return $files;
    }

    public static function rebuildBaseline()
    {
        $files = self::currentFiles();
        $db = Db::getInstance();
        $db->execute('DELETE FROM `' . _DB_PREFIX_ . 'datalog123_manifest`');
        foreach ($files as $path => $abs) {
            $hash = @hash_file('sha256', $abs);
            if ($hash === false || !$db->insert('datalog123_manifest', array('path' => pSQL($path), 'sha256' => pSQL($hash)), false, true, Db::INSERT_IGNORE)) {
                throw new RuntimeException('Could not store reference for: ' . $path);
            }
        }
        Configuration::updateValue('DATALOG123_BASELINE_AT', gmdate('Y-m-d H:i:s'));
        return count($files);
    }

    public static function compareBaseline()
    {
        $baselineAt = (string) Configuration::get('DATALOG123_BASELINE_AT');
        if (!$baselineAt) {
            return array('status' => 'baseline_required', 'added' => array(), 'modified' => array(), 'deleted' => array());
        }
        try {
            $baselineRows = Db::getInstance()->executeS('SELECT path, sha256 FROM `' . _DB_PREFIX_ . 'datalog123_manifest`');
            $baseline = array();
            foreach ((array) $baselineRows as $row) {
                $baseline[$row['path']] = $row['sha256'];
            }
            $files = self::currentFiles();
            $added = array();
            $modified = array();
            $deleted = array();
            foreach ($files as $path => $abs) {
                if (!isset($baseline[$path])) {
                    $added[] = $path;
                } elseif (($hash = @hash_file('sha256', $abs)) !== false && !hash_equals($baseline[$path], $hash)) {
                    $modified[] = $path;
                }
                unset($baseline[$path]);
            }
            $deleted = array_keys($baseline);
            sort($added); sort($modified); sort($deleted);
            return array(
                'status' => 'ok', 'baseline_at' => $baselineAt,
                'added_count' => count($added), 'modified_count' => count($modified), 'deleted_count' => count($deleted),
                'added' => array_slice($added, 0, self::MAX_CHANGES),
                'modified' => array_slice($modified, 0, self::MAX_CHANGES),
                'deleted' => array_slice($deleted, 0, self::MAX_CHANGES),
                'truncated' => count($added) > self::MAX_CHANGES || count($modified) > self::MAX_CHANGES || count($deleted) > self::MAX_CHANGES,
            );
        } catch (Exception $e) {
            return array('status' => 'error', 'message' => 'File comparison failed.');
        }
    }
}
