<?php
namespace AwkwardIdeas\Migrator;

use AwkwardIdeas\MyPDO\MyPDOServiceProvider;

class Migrator{
    private $connection;
    private $process=false;
    private $db;

    public function __construct()
    {
        $this->connection = self::GetConnectionData();
    }

    private function GetConnectionData(){
        $connection_host = "";
        $connection_database = "";
        $connection_username = "";
        $connection_password = "";

        if(isset($_POST['host'])) $connection_host = $_POST['host'];
        if(isset($_POST['database'])) $connection_database = $_POST['database'];
        if(isset($_POST['username'])) $connection_username = $_POST['username'];
        if(isset($_POST['password'])) $connection_password = $_POST['password'];

        if($connection_host!="" && $connection_database!="" && $connection_username!="" && $connection_password!="") $this->process=true;

        if(!$this->process) {
            $removeFromFileValue = "/[^a-zA-Z0-9]+/";
            $filePath = getcwd().'/.env';
            if (file_exists($filePath)) {
                $handle = @fopen($filePath, "r");
                if ($handle) {
                    while (($buffer = fgets($handle, 4096)) !== false) {
                        if (strpos(strtoupper($buffer), "DB_HOST=") > -1) {
                            $connection_host = preg_replace($removeFromFileValue, '', after("=", $buffer));
                        }
                        if (strpos(strtoupper($buffer), "DB_DATABASE=") > -1) {
                            $connection_database = preg_replace($removeFromFileValue, '', after("=", $buffer));
                        }
                        if (strpos(strtoupper($buffer), "DB_USERNAME=") > -1) {
                            $connection_username = preg_replace($removeFromFileValue, '', after("=", $buffer));
                        }
                        if (strpos(strtoupper($buffer), "DB_PASSWORD=") > -1) {
                            $connection_password = preg_replace($removeFromFileValue, '', after("=", $buffer));
                        }
                    }
                    if (!feof($handle)) {
                        echo "Error: unexpected fgets() fail\n";
                    }
                    fclose($handle);
                }
            }

            if($connection_host!="" && $connection_database!="" && $connection_username!="" && $connection_password!="") $this->process=true;
        }
        return ["host"=>$connection_host, "database"=>$connection_database, "username"=>$connection_username, "password"=>$connection_password];
    }

    public function GetHost(){
        return $this->connection["host"];
    }

    public function GetDatabase(){
        return $this->connection["database"];
    }

    public function SetDatabase($database){
        $this->connection["database"] =$database;
    }

    public function GetUsername(){
        return $this->connection["username"];
    }

    public function GetPassword(){
        return $this->connection["password"];
    }

    public function GetModels(){
        if(!$this->process) return;


    }

    public static function TruncateDatabase($database){
        $myLaravel = new Migrator();
        $myLaravel->SetDatabase($database);
        $query = "show tables;";
        $tables = $myLaravel->GetTables();

        foreach ($tables as $table) {
            $myLaravel->DeleteTable($table[0]);
        }
        return "Tables Deleted from $database";
    }

    public static function CleanMigrationsDirectory(){
        $myLaravel = new Migrator();
        $migrationsDirectory = $myLaravel->GetMigrationsDirectory();
        array_map('unlink', glob( "$migrationsDirectory*.php"));

        return "Migration Files Deleted in $migrationsDirectory";
    }

    public static function PrepareMigrations($database){
        $myLaravel = new Migrator();
        if($from!=""){
            $myLaravel->SetDatabase($database);
        }

        $tables = $myLaravel->GetTables();
        foreach ($tables as $table) {
            $tablename = $table[0];
            $myLaravel->CreateMigrationsFile($tablename);
        }
        return "New Migration Files Created in $migrationsDirectory";
    }

    public function GetMigrationsDirectory(){
        return getcwd().'/database/migrations/';
    }

    public static function Migrate(){
        $myLaravel = new Migrator();
        return $myLaravel->GetMigrations();
    }

    public function GetTables(){
        $query = "show tables;";
        $tables = $this->db->Query($query);
        return $tables;
    }

    public function DeleteTable($tablename){
        $query = "drop table `" . $tablename . "`;";
        return $this->db->Execute($query);
    }

    public function DescribeTable($tablename){
        $query = "describe `" . $tablename . "`;";
        $columns = $this->db->Query($query);
        return $columns;
    }

    public function CreateMigrationsFile($tablename){
        $fileData = $this->GetFileOutput($tablename);
        $fileName = $this->GetFileName($tablename);
        $dir = $myLaravel->GetMigrationsDirectory();

        $parts = explode('/', $dir);
        $file = array_pop($parts);
        $dir = '';
        foreach($parts as $part)
            if(!is_dir($dir .= "/$part")) mkdir($dir);
        return file_put_contents("$dir/$fileName", $fileData);
    }

    public function GetMigrations(){
        if(!$this->process) return;

        $output="";
        $tableLinks=[];
        $tableData=[];

        $tables = $this->GetTables();
        $output .= "<p>Found " . count($tables) . " tables.</p>";
        foreach ($tables as $table) {
            $tablename = $table[0];

            $eloquentData = self::GetFileOutput($tablename);

            $tableLinks[] = "<li role='presentation'><a href='#table-$tablename' aria-controls='table-$tablename' role='tab' data-toggle='tab'>$tablename</a></li>";
            $tableData[] = "<div role='tabpanel' class='tab-pane' id='table-$tablename'><div class='well'><div class=''form-group'><button class='btn btn-primary saveToFile' data-table='$tablename' style='margin-bottom:10px'><span class=\"glyphicon glyphicon-floppy-disk\" aria-hidden=\"true\"></span></button><label class='pull-right'>$tablename</label><textarea class='form-control input-lg' rows='15'>$eloquentData</textarea></div></div></div>";
        }

        unset($this->db);

        $output .= "<div><ul class=\"nav nav-pills\" role=\"tablist\">";
        foreach($tableLinks as $tableLink){
            $output .= $tableLink;
        }
        $output .= "</ul><div class=\"tab-content\" style='margin-top:50px'>";
        foreach($tableData as $data){
            $output .= $data;
        }
        $output .= "</div></div>";

        return $output;
    }

    private function ConnectToDatabase(){
        $this->db = new DB();
        $output="";
        if($this->db->EstablishConnections($this->GetHost(), $this->GetDatabase(), $this->GetUsername(), $this->GetPassword(), $this->GetUsername(), $this->GetPassword()))
            $output.= "<p>Connected to <b>".$this->GetDatabase()."</b> on <b>".$this->GetHost()."</b>.</p>";
        else
            $output.= "<p>Unable to connect. Please verify permissions.</p>";
        return $output;



    }

    private function GetFileOutput($tablename){
        $output="";
        $output = "<?php" . PHP_EOL
            . PHP_EOL
            . "use Illuminate\Database\Schema\Blueprint;" . PHP_EOL
            . "use Illuminate\Database\Migrations\Migration;" . PHP_EOL
            . PHP_EOL
            . "class Create".ucwords($tablename)."Table extends Migration" . PHP_EOL
            . "{" . PHP_EOL
            . indent() . "/**" . PHP_EOL
            . indent() . " * Run the migrations." . PHP_EOL
            . indent() . " *" . PHP_EOL
            . indent() . " * @return void" . PHP_EOL
            . indent() . " */" . PHP_EOL
            . indent() . "public function up()" . PHP_EOL
            . indent() . "{" . PHP_EOL
            . indent(2) . "if (!Schema::hasTable('" . $tablename . "')) {" . PHP_EOL;

        $schemaCreateWrapInject = "";
        $schemaTableWrapInject = "";

        $columns = $this->DescribeTable($tablename);
        $foreignKeys=[];
        $primaryKeys=[];
        $indexes=[];
        $uniques=[];
        $autoIncrement=[];

        foreach ($columns as $columndata) {
            $schemaCreateWrapInject .= indent(4) . self::AddColumnByDataType($tablename, $columndata) . ';' . PHP_EOL;
            if(strpos(strtoupper($columndata["Extra"]),"AUTO_INCREMENT") > -1){
                $autoIncrement[]=$columndata["Field"];
            }
            if(strpos(strtoupper($columndata["Key"]), "PRI") > -1 && ($columndata["Extra"] =="" || strpos(strtoupper($columndata["Extra"]),"AUTO_INCREMENT") == -1)){
                $primaryKeys[]=$columndata["Field"];
            }
            if (strpos(strtoupper($columndata["Key"]),"MUL") > -1) {
                $foreignKeys[]= self::GetForeignKeys($tablename, $columndata["Field"], indent(4));
            }
        }
        $inheritUnique = array_merge($autoIncrement, $primaryKeys);

        $indexes[] = self::GetIndexes($tablename, $inheritUnique, indent(4));
        $uniques[] = self::GetUniques($tablename, $inheritUnique, indent(4));
        if(count($primaryKeys)>0 && count($autoIncrement)==0){
            $identifierName = self::GetIdentifier($tablename, implode("_", $primaryKeys), "primary");
            if(count($primaryKeys)==1){
                $schemaTableWrapInject .= indent(4) . '$table->primary(\'' . implode($primaryKeys).'\',\''.$identifierName.'\');' . PHP_EOL;
            }else{
                $schemaTableWrapInject .= indent(4) . '$table->primary([\'' . implode('\',\'',$primaryKeys).'\'],\''.$identifierName.'\');' . PHP_EOL;
            }
        }
        $foreignKeys = array_filter($foreignKeys);
        if(count($foreignKeys) > 0){
            foreach($foreignKeys as $foreignKey){
                $schemaTableWrapInject .= $foreignKey;
            }
        }
        $indexes = array_filter($indexes);
        if(count($indexes) > 0){
            foreach($indexes as $index){
                $schemaTableWrapInject .= $index;
            }
        }
        $uniques = array_filter($uniques);
        if(count($uniques) > 0){
            foreach($uniques as $unique){
                $schemaTableWrapInject .= $unique;
            }
        }
        $output .= self::SchemaCreateWrap($tablename,$schemaCreateWrapInject,indent(3));
        $schemaCreateWrapInject="";
        $output .= self::SchemaTableWrap($tablename,$schemaTableWrapInject,indent(3));
        $schemaTableWrapInject="";

        //End Else
        $output .= indent(2) ."}else{" . PHP_EOL;

        $foreignKeys=[];
        $primaryKeys=[];
        $indexes=[];
        $uniques=[];
        $autoIncrement=[];

        foreach ($columns as $columndata) {
            $output .= indent(3) . 'if (!Schema::hasColumn(\'' . $tablename . '\', \'' . $columndata["Field"] . '\')) {' . PHP_EOL;
            $schemaTableWrapInject .= indent(5) . self::AddColumnByDataType($tablename, $columndata) . ';' . PHP_EOL;
            $output .= self::SchemaTableWrap($tablename,$schemaTableWrapInject,indent(4));
            $schemaTableWrapInject="";
            if(strpos(strtoupper($columndata["Extra"]),"AUTO_INCREMENT") > -1){
                $autoIncrement[]=$columndata["Field"];
            }
            if(strpos(strtoupper($columndata["Key"]), "PRI") > -1 && strpos(strtoupper($columndata["Extra"]),"AUTO_INCREMENT") == -1){
                $primaryKeys[]=$columndata["Field"];
            }
            if (strpos(strtoupper($columndata["Key"]),"MUL") > -1) {
                $foreignKeys[]= self::GetForeignKeys($tablename, $columndata["Field"], indent(5));
            }
            $output .= indent(3) . '}' . PHP_EOL
                . PHP_EOL;
        }
        $inheritUnique = array_merge($autoIncrement, $primaryKeys);

        $indexes[] = self::GetIndexes($tablename, $inheritUnique, indent(5));
        $uniques[] = self::GetUniques($tablename, $inheritUnique, indent(5));

        if(count($primaryKeys)>0 && count($autoIncrement)==0){
            $identifierName = self::GetIdentifier($tablename, implode("_", $primaryKeys), "primary");
            if(count($primaryKeys)==1){
                $schemaTableWrapInject .=  indent(4) . '$table->primary(\'' . implode($primaryKeys).'\',\''.$identifierName.'\');' . PHP_EOL;
            }else{
                $schemaTableWrapInject .=  indent(4) . '$table->primary([\'' . implode('\',\'',$primaryKeys).'\'],\''.$identifierName.'\');' . PHP_EOL;
            }
            $output .= self::SchemaTableWrap($tablename,$schemaTableWrapInject,indent(3));
            $schemaTableWrapInject="";
        }
        $foreignKeys = array_filter($foreignKeys);
        if(count($foreignKeys) > 0){
            foreach($foreignKeys as $foreignKey){
                $schemaTableWrapInject .= $foreignKey;
            }
            $output .= self::SchemaTableWrap($tablename,$schemaTableWrapInject,indent(3));
            $schemaTableWrapInject="";
        }

        $indexes = array_filter($indexes);
        if(count($indexes) > 0){
            foreach($indexes as $index){
                $schemaTableWrapInject .= $index;
            }
            $output .= self::SchemaTableWrap($tablename,$schemaTableWrapInject,indent(3));
            $schemaTableWrapInject="";
        }

        $uniques = array_filter($uniques);
        if(count($uniques) > 0){
            foreach($uniques as $unique){
                $schemaTableWrapInject .= $unique;
            }
            $output .= self::SchemaTableWrap($tablename,$schemaTableWrapInject,indent(3));
            $schemaTableWrapInject="";
        }

        $output .= indent(2) . "}" . PHP_EOL //End Else
            . indent() . "}" . PHP_EOL //End Up()
            . PHP_EOL;

        $output .= indent() . "/**" . PHP_EOL
            . indent() . " * Reverse the migrations." . PHP_EOL
            . indent() . " *" . PHP_EOL
            . indent() . " * @return void" . PHP_EOL
            . indent() . " */" . PHP_EOL
            . indent() . "public function down()" . PHP_EOL
            . indent() . "{" . PHP_EOL
            . self::DropForeignKeys($tablename, indent(2))
            . indent(2) . "Schema::drop('$tablename');" . PHP_EOL
            . indent() . "}" . PHP_EOL;

        $output .= indent() . "/**" . PHP_EOL
            . indent() . " *" . PHP_EOL;
        foreach ($columns as $columndata) {
            $output .= indent() . " * " . $columndata["Field"] . "	" . $columndata["Type"] . "	" . $columndata["Null"] . "	" . $columndata["Key"] . "	" . $columndata["Default"] . "	" . $columndata["Extra"] . "	" . PHP_EOL;
        }
        $output .= indent() . " *" . PHP_EOL
            . indent() . " */" . PHP_EOL
            . "}";

        return $output;
    }

    private function GetFileName($tablename){
        $d = date('Y_m_d_His');
        return "$d_create_$tablename_table.php";
    }

    private function AddColumnByDataType($tablename, $coldata)
    {
        $name = $coldata["Field"];
        $typedata = $coldata["Type"];
        $null = $coldata["Null"];
        $key = $coldata["Key"];
        $default = $coldata["Default"];
        $extra = $coldata["Extra"];

        $type = before('(', $typedata);
        $data = between('(', ')', $typedata);
        $info = after(')', $typedata);

        $eloquentCall = '$table->';

        switch (strtoupper($type)) {
            //      $table->bigIncrements('id');	Incrementing ID (primary key) using a "UNSIGNED BIG INTEGER" equivalent.
            //      $table->bigInteger('votes');	BIGINT equivalent for the database.
            case 'BIGINT':
                if (strpos(strtoupper($extra),"AUTO_INCREMENT") > -1) {
                    $eloquentCall .= 'bigIncrements(\'' . $name . '\')';
                } else {
                    $eloquentCall .= 'bigInteger(\'' . $name . '\')';
                }
                break;
            //      $table->binary('data');	BLOB equivalent for the database.
            case 'BINARY':
                $eloquentCall .= 'binary(\'' . $name . '\')';
                break;
            case 'BIT':
                $eloquentCall .= 'boolean(\'' . $name . '\')';
                if($default!=""){
                    $default = (strpos($default,'0')>-1) ? "0" : "1";
                }
                break;
            //      $table->boolean('confirmed');	BOOLEAN equivalent for the database.
            case 'BOOLEAN':
                $eloquentCall .= 'boolean(\'' . $name . '\')';
                break;
            //      $table->char('name', 4);	CHAR equivalent with a length.
            case 'CHAR':
                $eloquentCall .= 'char(\'' . $name . '\', ' . $data . ')';
                break;
            //      $table->date('created_at');	DATE equivalent for the database.
            case 'DATE':
                $eloquentCall .= 'date(\'' . $name . '\')';
                break;
            //      $table->dateTime('created_at');	DATETIME equivalent for the database.
            case 'DATETIME':
                $eloquentCall .= 'dateTime(\'' . $name . '\')';
                break;
            //      $table->decimal('amount', 5, 2);	DECIMAL equivalent with a precision and scale.
            case 'DECIMAL':
                $eloquentCall .= 'decimal(\'' . $name . '\', ' . $data . ')';
                break;
            //      $table->double('column', 15, 8);	DOUBLE equivalent with precision, 15 digits in total and 8 after the decimal point.
            case 'DOUBLE':
                $eloquentCall .= 'double(\'' . $name . '\', ' . $data . ')';
                break;
            //      $table->enum('choices', ['foo', 'bar']);	ENUM equivalent for the database.
            case 'ENUM':
                $eloquentCall .= 'enum(\'' . $name . '\', [' . $data . '])';
                break;
            //      $table->float('amount');	FLOAT equivalent for the database.
            case 'FLOAT':
                $eloquentCall .= 'float(\'' . $name . '\')';
                break;
            //      $table->increments('id');	Incrementing ID (primary key) using a "UNSIGNED INTEGER" equivalent.
            //      $table->integer('votes');	INTEGER equivalent for the database.
            case 'INT':
                if (strpos(strtoupper($extra),"AUTO_INCREMENT") > -1) {
                    $eloquentCall .= 'increments(\'' . $name . '\')';
                } else {
                    $eloquentCall .= 'integer(\'' . $name . '\')';
                }
                break;
            //      $table->json('options');	JSON equivalent for the database.
            case 'JSON':
                $eloquentCall .= 'json(\'' . $name . '\')';
                break;
            //      $table->jsonb('options');	JSONB equivalent for the database.
            case 'JSONB':
                $eloquentCall .= 'jsonb(\'' . $name . '\')';
                break;
            //      $table->longText('description');	LONGTEXT equivalent for the database.
            case 'LONGTEXT':
                $eloquentCall .= 'longText(\'' . $name . '\')';
                break;
            //      $table->mediumInteger('numbers');	MEDIUMINT equivalent for the database.
            case 'MEDIUMINT':
                $eloquentCall .= 'mediumInteger(\'' . $name . '\')';
                break;
            //      $table->mediumText('description');	MEDIUMTEXT equivalent for the database.
            case 'MEDIUMTEXT':
                $eloquentCall .= 'mediumText(\'' . $name . '\')';
                break;
            //      $table->morphs('taggable');	Adds INTEGER taggable_id and STRING taggable_type.
            case 'MORPHS':
                $eloquentCall .= 'morphs(\'' . $name . '\')';
                break;
            //      $table->nullableTimestamps();	Same as timestamps(), except allows NULLs.
            case 'NULL_TIMESTAMPS':
                $eloquentCall .= 'nullableTimestamps()';
                break;
            //      $table->rememberToken();	Adds remember_token as VARCHAR(100) NULL.
            case 'REMEMBER':
                $eloquentCall .= 'rememberToken()';
                break;
            //      $table->smallInteger('votes');	SMALLINT equivalent for the database.
            case 'SMALLINT':
                $eloquentCall .= 'smallInteger(\'' . $name . '\')';
                break;
            //      $table->softDeletes();	Adds deleted_at column for soft deletes.
            case 'SOFTDELETES':
                $eloquentCall .= 'softDeletes()';
                break;
            //      $table->string('email');	VARCHAR equivalent column.
            //      $table->string('name', 100);	VARCHAR equivalent with a length.
            case 'VARCHAR':
                if ($data != "") {
                    $eloquentCall .= 'string(\'' . $name . '\', ' . $data . ')';
                } else {
                    $eloquentCall .= 'string(\'' . $name . '\')';
                }
                break;
            //      $table->text('description');	TEXT equivalent for the database.
            case 'TEXT':
                $eloquentCall .= 'text(\'' . $name . '\')';
                break;
            //      $table->time('sunrise');	TIME equivalent for the database.
            case 'TIME':
                $eloquentCall .= 'time(\'' . $name . '\')';
                break;
            //      $table->tinyInteger('numbers');	TINYINT equivalent for the database.
            case 'TINYINT':
                if($data==1){
                    $eloquentCall .= 'boolean(\'' . $name . '\')';
                }else{
                    $eloquentCall .= 'tinyInteger(\'' . $name . '\')';
                }
                break;
            //      $table->timestamp('added_on');	TIMESTAMP equivalent for the database.
            case 'TIMESTAMP':
                $eloquentCall .= 'timestamp(\'' . $name . '\')';
                break;
            //      $table->timestamps();	Adds created_at and updated_at columns.
            case 'TIMESTAMPS':
                $eloquentCall .= 'timestamps()';
                break;
            //      $table->uuid('id');
            case 'YEAR':
                $eloquentCall .= 'tinyInteger(\'' . $name . '\')';
                break;
            case 'UUID':
                $eloquentCall .= 'uuid(\'' . $name . '\')';
                break;
            default:
                return false;
        }

        if(strpos(strtoupper($info), " UNSIGNED") > -1){
            $eloquentCall .= "->unsigned()";
        }

        if(strtoupper($null) == "YES"){
            $eloquentCall .= "->nullable()";
        }

        if($default != ""){
            if($default=="CURRENT_TIMESTAMP"){
                $eloquentCall .= "->useCurrent()";
                //Needs on update use current_timestamp feature if in extra
            }else{
                $eloquentCall .= "->default('".addslashes($default)."')";
            }

        }

        return $eloquentCall;
    }

    private function GetIndexes($tablename, $primaryKeys,$indentation){
        $schemaname = $this->GetDatabase();
        $sqlQuery = "SELECT DISTINCT GROUP_CONCAT(COLUMN_NAME) as COLUMN_NAME FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA=:schemaname AND TABLE_NAME=:tablename AND Non_unique=1 AND INDEX_NAME <> 'PRIMARY' GROUP BY INDEX_NAME;";
        $relations = $this->db->Query($sqlQuery, [new SQLParameter(":schemaname",$schemaname), new SQLParameter(":tablename",$tablename)]);
        $indexCall="";
        foreach($relations as $relation) {
            $columns = $relation['COLUMN_NAME'];
            if(in_array($columns, $primaryKeys)){
                continue;
            }
            $columns = array_filter(explode(",",$columns));
            $identifierName = self::GetIdentifier($tablename, implode("_", $columns), "index");
            if (count($columns) > 1) {

                $indexCall .= $indentation . '$table->index([\'' . implode("','", $columns) . '\'], \'' . $identifierName . '\');' . PHP_EOL;
            } else {
                $indexCall .= $indentation . '$table->index(\'' . implode($columns) . '\', \'' . $identifierName . '\');' . PHP_EOL;
            }
        }
        return $indexCall;
    }

    private function GetUniques($tablename, $primaryKeys, $indentation){
        $schemaname = $this->GetDatabase();
        $sqlQuery = "SELECT DISTINCT GROUP_CONCAT(COLUMN_NAME) as COLUMN_NAME FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA=:schemaname AND TABLE_NAME=:tablename AND Non_unique=0 AND INDEX_NAME <> 'PRIMARY' GROUP BY INDEX_NAME;";
        $relations = $this->db->Query($sqlQuery, [new SQLParameter(":schemaname",$schemaname), new SQLParameter(":tablename",$tablename)]);
        $uniqueCall="";

        foreach($relations as $relation) {
            $columns = $relation['COLUMN_NAME'];
            if(in_array($columns, $primaryKeys)){
                continue;
            }
            $columns = array_filter(explode(",",$columns));
            $identifierName = self::GetIdentifier($tablename, implode("_", $columns), "unique");
            if (count($columns) > 1) {
                $uniqueCall .= $indentation . '$table->unique([\'' . implode("','", $columns) . '\'], \'' . $identifierName . '\');' . PHP_EOL;
            } else {
                $uniqueCall .= $indentation . '$table->unique(\'' . implode($columns) . '\', \'' . $identifierName . '\');' . PHP_EOL;
            }
        }
        return $uniqueCall;
    }

    private function GetForeignKeys($tablename, $columnname, $indentation){
        $schemaname = $this->GetDatabase();
        $sqlQuery = "SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=:schemaname AND TABLE_NAME=:tablename AND COLUMN_NAME=:columnname AND REFERENCED_TABLE_NAME IS NOT NULL AND REFERENCED_COLUMN_NAME IS NOT NULL;";
        $relations = $this->db->Query($sqlQuery, [new SQLParameter(":schemaname",$schemaname), new SQLParameter(":tablename",$tablename), new SQLParameter(":columnname",$columnname)]);
        $foreignCall="";
        foreach($relations as $relation) {
            $foreignCall.= $indentation . '$table->foreign(\'' . $relation['COLUMN_NAME'] . '\')->references(\'' . $relation['REFERENCED_COLUMN_NAME'] . '\')->on(\'' . $relation['REFERENCED_TABLE_NAME'] . '\');' . PHP_EOL;
        }
        return $foreignCall;
    }

    private function DropForeignKeys($tablename, $indentation){
        $schemaname = $this->GetDatabase();
        $sqlQuery = "SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=:schemaname AND TABLE_NAME=:tablename AND REFERENCED_TABLE_NAME IS NOT NULL AND REFERENCED_COLUMN_NAME IS NOT NULL;";
        $relations = $this->db->Query($sqlQuery, [new SQLParameter(":schemaname",$schemaname), new SQLParameter(":tablename",$tablename)]);
        $foreignCall="";
        foreach($relations as $relation) {
            $foreignCall.= $indentation . indent() . '$table->dropForeign([\'' . $relation['COLUMN_NAME'] . '\']);' . PHP_EOL;
        }

        if($foreignCall!=""){
            $foreignCall = self::SchemaTableWrap($tablename,$foreignCall,$indentation);
        }

        return $foreignCall;
    }

    private function SchemaCreateWrap($tablename, $content, $indentation){
        $wrap = $indentation . 'Schema::create(\'' . $tablename . '\', function (Blueprint $table){' . PHP_EOL
            . $content
            . $indentation . '});' . PHP_EOL;

        return $wrap;
    }

    private function SchemaTableWrap($tablename, $content, $indentation){
        $wrap = $indentation . 'Schema::table(\'' . $tablename . '\', function ($table) {' . PHP_EOL
            . $content
            . $indentation . '});' . PHP_EOL;

        return $wrap;
    }

    private function GetIdentifier($tablename, $columns, $type){
        $maxCharacters = 60; //64, but reducing to avoid issues
        $identifier = $tablename."_".$columns."_".$type;
        if(strlen($identifier) > $maxCharacters){
            $constraint = strlen($tablename."_".$type);
            $columns = explode("_",$columns);
            $remainder = $maxCharacters - $constraint - count($columns);
            $permit = ($remainder - ($remainder % count($columns))) / count($columns);
            $identifier =$tablename."_";
            foreach($columns as $column){
                $identifier .= substr($column,0,$permit)."_";
            }
            $identifier .= $type;
        }
        return $identifier;
    }
}