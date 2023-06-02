<?php

namespace zFramework\Kernel\Modules;

use zFramework\Core\Facades\DB as FacadesDB;
use zFramework\Kernel\Terminal;

class Db
{
    static $db;
    static $dbname;
    static $tables = null;

    public static function begin()
    {
        global $databases;
        self::connectDB(Terminal::$parameters['db'] ?? array_keys($databases)[0]);
        self::{Terminal::$commands[1]}();
    }

    private static function connectDB($db)
    {
        self::$db     = new FacadesDB($db);
        self::$dbname = self::$db->prepare('SELECT database() AS dbname')->fetch(\PDO::FETCH_ASSOC)['dbname'];
    }

    private static function table_exists($table = null)
    {
        if (!empty($table)) Terminal::$parameters['table'] = $table;

        if (!self::$tables) {
            $tables = self::$db->prepare("SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = :dbname", ['dbname' => self::$dbname])->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($tables as $key => $table) $tables[$key] = $table['TABLE_NAME'];
            self::$tables = $tables;
        }

        if (in_array(Terminal::$parameters['table'], self::$tables)) return true;
        return false;
    }

    public static function migrate()
    {
        global $databases;

        $MySQL_defines = [
            'CURRENT_TIMESTAMP'
        ];

        $path       = Terminal::$parameters['path'] ?? null;
        $migrations = glob(BASE_PATH . '\database\migrations\\' . ($path ? "$path\\" : null) . '*.php');
        if (!count($migrations)) {
            Terminal::text("You haven't a migration.", 'red');
            if ($path) Terminal::text("in $path", 'yellow');
            return false;
        }

        foreach ($migrations as $migration) {
            if (!in_array($migration, \zFramework\Run::$included)) \zFramework\Run::includer($migration);

            $class = str_replace(['.php', BASE_PATH], '', ucfirst(@end(explode('\\', $migration))));
            // control
            if (!class_exists($class)) {
                Terminal::text("There are not a $class migrate class.", 'red');
                continue;
            }

            if (!isset($databases[$class::$db])) {
                Terminal::text($class::$db . " database is not exists.", 'red');
                continue;
            }
            # connect to model's database
            self::connectDB($class::$db);

            $columns = $class::columns();

            # setting prefix.
            if (isset($class::$prefix)) foreach ($columns as $name => $val) {
                unset($columns[$name]);
                $name = ($class::$prefix ? $class::$prefix . "_" : null) . $name;
                $columns[$name] = $val;
            }
            #
            # Setting consts
            $consts = config('model.consts');
            if (strlen($key = array_search('timestamps', $columns))) {
                unset($columns[$key]);
                $columns = array_merge($columns, [
                    $consts['updated_at'] => ['required', 'datetime', 'default:CURRENT_TIMESTAMP', 'onupdate'],
                    $consts['created_at'] => ['required', 'datetime', 'default:CURRENT_TIMESTAMP'],
                ]);
            }

            if (strlen($key = array_search('softDelete', $columns))) {
                unset($columns[$key]);
                $columns = array_merge($columns, [$consts['deleted_at'] => ['nullable', 'datetime', 'default']]);
            }
            #

            $charset = $class::$charset ?? null;
            $table   = $class::$table;
            //

            echo str_repeat(PHP_EOL, 2);
            Terminal::text("`$table` migrating:", 'green');

            $drop_columns = [];

            # Reset Table.
            $migrate_fresh = in_array('--fresh', Terminal::$parameters) ?? false;
            if (!$migrate_fresh && !self::table_exists($table)) $migrate_fresh = true;

            if ($migrate_fresh) {
                Terminal::text('Info: Migrate forcing.', 'blue');

                $init_column_name = "table_initilazing";
                try {
                    self::$db->prepare("DROP TABLE $table");
                } catch (\PDOException $e) {
                    // Terminal::text($e->getMessage(), 'red');
                }
                self::$db->prepare("CREATE TABLE $table ($init_column_name int DEFAULT 1 NOT NULL)" . ($charset ? " CHARACTER SET " . strtok($charset, '_') . " COLLATE $charset" : null));

                $drop_columns[] = $init_column_name;
            }
            #

            # Migrate stuff
            $last_column = null;
            foreach ($columns as $column => $parameters) {
                $data = [
                    'type' => 'INT'
                ];

                try {
                    self::$db->prepare("ALTER TABLE $table DROP index $column;");
                } catch (\PDOException $e) {
                    // Terminal::text('Warning: ' . $e->getMessage(), 'yellow');
                }

                foreach ($parameters as $parameter) {
                    $switch = explode(':', $parameter);
                    switch ($switch[0]) {
                        case 'primary':
                            $data['index'] = " PRIMARY KEY AUTO_INCREMENT ";
                            break;

                        case 'required':
                            $data['nullstatus'] = " NOT NULL ";
                            break;

                        case 'nullable':
                            $data['nullstatus'] = " NULL ";
                            break;

                        case 'unique':
                            $data['extras'][] = " ADD UNIQUE (`$column`) ";
                            break;

                            # String: start
                        case 'text':
                            $data['type'] = " TEXT ";
                            break;

                        case 'longtext':
                            $data['type'] = " LONGTEXT ";
                            break;

                        case 'varchar':
                            $data['type'] = " VARCHAR(" . ($switch[1] ?? 255) . ") ";
                            break;

                        case 'char':
                            $data['type'] = " CHAR(" . ($switch[1] ?? 50) . ") ";
                            break;

                        case 'json':
                            $data['type'] = " JSON ";
                            break;
                            # String: end

                            # INT: start
                        case 'bigint':
                            $data['type'] = " BIGINT ";
                            break;

                        case 'int':
                            $data['type'] = " INT ";
                            break;

                        case 'smallint':
                            $data['type'] = " SMALLINT ";
                            break;

                        case 'tinyint':
                            $data['type'] = " TINYINT ";
                            break;

                        case 'bool':
                            $data['type'] = " TINYINT(1) ";
                            break;

                        case 'decimal':
                            $data['type'] = " DECIMAL ";
                            break;

                        case 'float':
                            $data['type'] = " FLOAT ";
                            break;
                            # INT: end

                            # Date: start
                        case 'date':
                            $data['type'] = " DATE ";
                            break;

                        case 'datetime':
                            $data['type'] = " DATETIME ";
                            break;

                        case 'time':
                            $data['type'] = " TIME ";
                            break;
                            # Date: end

                        case 'default':
                            $data['default'] = " DEFAULT" . (@$switch[1] ? (!in_array($switch[1], $MySQL_defines) ? ((is_numeric($switch[1]) ? " " . $switch[1] : " '" . addslashes($switch[1]) . "' ")) : (" " . $switch[1])) : ' NULL') . " ";
                            break;

                        case 'charset':
                            $data['charset'] =  " CHARACTER SET " . strtok($switch[1], '_') . " COLLATE " . $switch[1] . " ";
                            break;

                        case 'onupdate':
                            $data['default'] = $data['default'] . " ON UPDATE CURRENT_TIMESTAMP";
                            break;
                    }
                }

                $buildSQL = str_replace(['  ', ' ;'], [' ', ';'], ("ALTER TABLE $table ADD $column " . (@$data['type'] . @$data['charset'] . @$data['nullstatus'] . @$data['default'] . @$data['index']) . ($last_column ? " AFTER $last_column " : ' FIRST ') . (isset($data['extras']) ? ", " . implode(', ', $data['extras']) : null) . ";"));

                $while = ['loop' => true, 'continue' => false, 'status' => 0];
                while ($while['loop'] == true) {
                    try {
                        self::$db->prepare($buildSQL);
                        # insert edildiği anlamına geliyor.
                        if ($while['status'] == 0) $while['status'] = 1;
                        #
                        $while['loop'] = false;
                    } catch (\PDOException $e) {
                        switch ((string) $e->errorInfo['1']) {
                            case '1060':
                                $buildSQL = str_replace("$table ADD", "$table MODIFY", $buildSQL);
                                $while['status'] = 2;
                                break;

                            case '1068':
                                $while['status'] = 3;
                                $while['loop'] = false;
                                break;

                            default:
                                Terminal::text('Unkown Error: ' . $e->getMessage(), 'red');
                                $while['loop'] = false;
                                continue;
                        }
                    }
                }

                $types = [3 => ['not changed.', 'dark-gray'], 1 => ['added', 'green'], 2 => ['modified', 'yellow']];
                Terminal::text("-> `$column` " . $types[$while['status']][0], $types[$while['status']][1]);


                $last_column = $column;
            }
            #

            foreach ($drop_columns as $drop) {
                try {
                    self::$db->prepare("ALTER TABLE $table DROP COLUMN $drop");
                    Terminal::text("Dropped column: $drop", 'yellow');
                } catch (\PDOException $e) {
                    Terminal::text("Error: Column is can not drop: $drop", 'red');
                }
            }

            Terminal::text("`$table` migrate complete.", 'green');
        }
    }

    public static function seed()
    {
        $seeders = glob('database\seeders\*.php');
        if (!count($seeders)) return Terminal::text("You haven't any seeder.", 'red');
        foreach ($seeders as $inc) {
            if (!in_array($inc, \zFramework\Run::$included)) \zFramework\Run::includer($inc);

            $className = ucfirst(str_replace(['.php', '/'], ['', '\\'], $inc));
            call_user_func_array([new $className(), 'seed'], []);
            Terminal::text("$className seeded.", 'green');
        }

        return true;
    }
}
