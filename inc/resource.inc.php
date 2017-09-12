<?php



class Resource
{

    function __construct($table, $labels)
    {
        global $mysqli;

        $this->mysqli = $mysqli;
        $this->table = $table;
        $this->labels = $labels;
        $this->columns = $mysqli->query("DESCRIBE $table")->fetch_all();
        $this->pk = $this->findPk($this->columns);
    }

    function findPk($columns)
    {
        return array_filter($columns, function($column) {
            return $column[3] == 'PRI';
        })[0][0];
    }


    /**
     * Display functions
     */
    function display()
    {
        $result = $this->mysqli->query("SELECT * FROM $this->table");

        echo "
            <h1>$this->table</h1>
            <br/>
            <input type='button' onclick='document.location.href=\"?action=addjob\"' value='Baan toevoegen' />
            <br/>
            <br/>

            <table>";


        echo "<tr>";
        foreach ($this->labels as $column => $label)
            echo "<th>{$label}</th>";
        echo "<th>Actie</th>";
        echo "</tr>";


        while($row = $result->fetch_assoc()) {
            $row = escapeArray($row); // alle slashes weghalen
            echo"<tr>";
            foreach ($this->labels as $column => $label) {
                echo"<td>".$row[$column]."</td>";
            }
            echo"<td>
                    <a href=\"index.php?action=editjob&id=".$row[$this->pk]."\">Bewerken</a>
                    |
                    <a href=\"javascript:confirmAction('Zeker weten?', 'index.php?action=deletejob&id=".$row[$this->pk]."');\">Verwijderen</a>
                </td>
            </tr>";
        }

        echo"</table>";
    }

    function displayAdd()
    {
        echo"
            <h1>Baan bewerken</h1>
            <form method=\"post\" action=\"index.php?action=insertjob\">
             <table> ";

                 foreach ($this->labels as $column => $label) {
                     echo"		<tr>";
                     echo"			<td>$label:</td>";
                     echo"			<td><input type=\"text\" name=\"$column\"/></td>";
                     echo"		</tr>";
                 }

        echo"		<tr>";
        echo"			<td></td>";
        echo"			<td><input type=\"submit\" value=\"Opslaan\" /></td>";
        echo"		</tr>";

        echo "
            </table>
        </form>";
    }

    function displayEdit()
    {

        $sql = sprintf( "SELECT * FROM $this->table WHERE `$this->pk` = %d",
                        $this->mysqli->escape_string($_GET['id']) );

        $result = $this->mysqli->query($sql);

        if($row = $result->fetch_assoc()) {

            $row = escapeArray($row); // alle slashes weghalen

            echo"<h1>Baan bewerken</h1>";

            echo" <form method=\"post\" action=\"index.php?action=updatejob\">";
            echo" 	<table>";

            foreach ($this->labels as $column => $label) {
                echo"		<tr>";
                echo"			<td>$label:</td>";
                echo"			<td><input type=\"text\" name=\"$column\" value=\"".$row[$column]."\" /></td>";
                echo"		</tr>";
            }

            echo"		<tr>";
            echo"			<td></td>";
            echo"			<td><input type=\"submit\" value=\"Opslaan\" /></td>";
            echo"		</tr>";
            echo" 	</table>";

            echo" <input type=\"hidden\" name=\"JobID\" value=\"".$row[$this->pk]."\" />";

            echo" </form>";

        }
        else {
            die("Geen gegevens gevonden");
        }
    }


    /**
     * CRUD functions
     */
    function add()
    {

        // Letop we maken gebruik van sprintf. Kijk op php.net voor meer info.
        // Binnen sprintf staat %s voor een string, %d voor een decimaal (integer) en %f voor een float

        $sql = sprintf("INSERT INTO `$this->table` (`JobTitle`, `MinSalary`, `MaxSalary`) VALUES  ('%s', '%f', '%f')",
                        $this->mysqli->escape_string($_POST['JobTitle']),
                        $this->mysqli->escape_string($_POST['MinSalary']),
                        $this->mysqli->escape_string($_POST['MaxSalary']) );

        $mysqli->query($sql);

        header("location: index.php?action=jobs"); // terugkeren naar jobs
        exit();
    }

    function update()
    {
        global $mysqli;
        $sql = sprintf("UPDATE `Jobs` SET
                        `JobTitle` = '%s',
                        `MinSalary` = '%s',
                        `MaxSalary` = '%s'
                        WHERE `JobID` = %d",
                        $mysqli->escape_string($_POST['JobTitle']),
                        $mysqli->escape_string($_POST['MinSalary']),
                        $mysqli->escape_string($_POST['MaxSalary']),
                        $mysqli->escape_string($_POST['JobID']) );

        $mysqli->query($sql);

        header("location: index.php?action=jobs"); // terugkeren naar jobs
        exit();
    }

    function delete()
    {

        $sql = sprintf("DELETE FROM `$this->table` WHERE `$this->pk` = %d", $mysqli->escape_string($_GET['id']));
        $this->mysqli->query($sql);
        header("location: index.php?action=jobs"); // terugkeren naar jobs
        exit();
    }
}
